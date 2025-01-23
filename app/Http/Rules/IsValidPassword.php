<?php

/**
 * This file contains all possible ways of adding a custom rule.
 */

namespace ReadmeDisplay\App\Http\Rules;

class IsValidPassword
{
	public function __invoke($attr, $value, $rules, $data, ...$params)
	{
		// $params = ['param1', 'param2'] (Passed from method call)
		// i.e: Rule::isValidPassword('param1', 'param2')

		if (!preg_match('/[@_!#$%^&*()<>?\/|}{~:]/', $value)) {
            return "The {$attr} field must contain special characters.";
        }
	}
}

// Add the defined custom rule above in a controller or in any file
// in the project before you use this rule in your controller class.
// Import the Rule class from the "vendor/Validator" directory.
// -----------------------------------------------------------------
// Rule::add('is_valid_password', \ReadmeDisplay\App\Http\Rules\IsValidPassword::class);
// It's also possible to ommit the first key, the snake_kase class name will be used
// Rule::add(\ReadmeDisplay\App\Http\Rules\IsValidPassword::class);

// Or this way using a closure in the Controller class:
// -----------------------------------------------------------------
// Rule::add('is_valid_password', new class {
//     public function __invoke($attr, $value, $rules, $data, ...$params) {
//         // $params = ['param1', 'param2'] (Passed from method call)
//         // i.e: Rule::isValidPassword('param1', 'param2')
//         
//         if (!preg_match('/[@_!#$%^&*()<>?\/|}{~:]/', $value)) {
//             return "The {$attr} field must contain special characters.";
//         }
//     }
// });

// Or this way using an anonymous class in the Controller class:
// -----------------------------------------------------------------
// Rule::add('is_valid_password', function($attr, $value, $rules, $data, ...$params) {
//     // $params = ['param1', 'param2'] (Passed from method call)
//     // i.e: Rule::isValidPassword('param1', 'param2')
//     
//     if (!preg_match('/[@_!#$%^&*()<>?\/|}{~:]/', $value)) {
//         return "The {$attr} field must contain special characters.";
//     }
// });

// Example of using the custom rule:
// -----------------------------------------------------------------
// $data = $this->validate($request->all(), [
//     'password' => Rule::isValidPassword('param1', 'param2'),
// -----------------------------------------------------------------
//     Or this way in the rules array directly
//     'name' => [
//         'is_clean:param1,param2' => function($attr, $value, $rules, $data, ...$params) {
// 			   // $params = ['param1', 'param2']
//             if (preg_match('/[@_!#$%^&*()<>?\/|}{~:]/', $value)) {
//                 return "The {$attr} field cannot contain any special characters.";
//             }
//         }
//     ]
// ]);
