<?php

return new class {

	/**
     * Format the menu items.
     * @param  array  $menuItems
     * @return array
     */
    protected function formatMenuItems(array $menuItems)
    {
        foreach ($menuItems as &$menuItem) {
            $menuItem['component'] = $menuItem['component'] ?? '';
            $menuItem['attributes'] = $menuItem['attributes'] ?? [];
            $menuItem['attributes'] = $this->formatAttributes(
                $menuItem['attributes']
            );

            if (
                isset($menuItem['sub_items']) &&
                is_array($menuItem['sub_items'])
            ) {
                $menuItem['sub_items'] = $this->formatMenuItems(
                    $menuItem['sub_items']
                );
            }
        }

        return $menuItems;
    }

    /**
     * Format the attributes array to string.
     * 
     * @param  array  $attributes
     * @return string
     */
    protected function formatAttributes(array $attributes)
    {
        $formatted = [];

        foreach ($attributes as $attribute) {
            if (is_string($attribute)) {
                $formatted[] = htmlspecialchars($attribute, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($attribute)) {
                foreach ($attribute as $key => $value) {
                    $formatted[] = sprintf(
                        '%s="%s"',
                        htmlspecialchars($key, ENT_QUOTES, 'UTF-8'),
                        htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
                    );
                }
            }
        }

        return implode(' ', $formatted);
    }
    
	/**
     * Build the menu.
     *
     * @param array $menuItems Array of menu items.
     * @return string Rendered menu HTML.
     */
    protected function buildMenu(array $menuItems)
    {
        $output = '<ul class="fframe_menu">';
        foreach ($menuItems as $item) {
            $output .= $this->renderMenuItem($item);
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Render a single menu item.
     *
     * @param array $item Menu item data.
     * @param bool $isSubmenu Indicates if the item is part of a submenu.
     * @return string Rendered menu item HTML.
     */
    protected function renderMenuItem(array $item, bool $isSubmenu = false)
    {
        $hasSubMenu = !empty($item['sub_items']);
        $key = esc_attr($item['key']);
        $label = sanitize_text_field($item['label']);
        $permalink = esc_url($item['permalink']);
        $component = esc_attr($item['component']);
        $attributes = esc_attr($item['attributes']);

        $classes = 'fframe_menu_item ' . ($isSubmenu ? 'fframe_submenu_item' : '') . ($hasSubMenu ? ' fframe_has_subItems' : '') . ' fframe_item_' . $key;

        $itemHtml = "<li data-key='{$key}' class='{$classes}'>";
        $itemHtml .= "<a {$attributes} class='" . ($isSubmenu ? 'fframe_submenu_link' : 'fframe_menu_primary') . "'";
        $itemHtml .= $component ? " data-component='{$component}'" : '';
        $itemHtml .= " href='{$permalink}'" . ($hasSubMenu ? " aria-expanded='false'" : '') . ">";
        $itemHtml .= $label;
        if ($hasSubMenu && !$isSubmenu) {
            $itemHtml .= "<span class='dashicons dashicons-arrow-down-alt2'></span>";
        }
        $itemHtml .= "</a>";

        // Render submenu if it exists
        if ($hasSubMenu) {
            $itemHtml .= "<ul class='fframe_submenu_items'>";
            foreach ($item['sub_items'] as $subItem) {
                $itemHtml .= $this->renderMenuItem($subItem, true);
            }
            $itemHtml .= "</ul>";
        }

        $itemHtml .= "</li>";

        return $itemHtml;
    }

    public function __invoke($menuItems)
    {
    	return $this->buildMenu(
    		$this->formatMenuItems($menuItems)
    	);
    }
};
