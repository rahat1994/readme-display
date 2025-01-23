<div id="<?php echo $slug; ?>-app" class="warp fconnector_app">
    <div class="fframe_app">
        
        <!-- Navbar -->
        <div class="fframe_main-menu-items">

            <!-- Brand Logo -->
            <div class="menu_logo_holder">
                <a href="<?php echo esc_url($baseUrl); ?>">
                    <img
                        style="max-height: 40px;"
                        src="<?php echo esc_url($logo); ?>"
                    /> <span>beta</span>
                </a>
            </div>

            <!-- Menu -->
            <ul class="fframe_menu">
                <?php echo (require('builder.php'))($menuItems); ?>
            </ul>
        </div>

        <!-- Content -->
        <div class="fframe_body">
            <div id="fluent-framework-app" data-container></div>
        </div>
    </div>
</div>
