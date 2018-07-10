<?php

// max 4096
// bottom_footer 	include/bottom_footer.php 	Allows you to override the rendering of the bottom_footer html snippet. 
// custom_login 	auth_login.php 			Allows you to override the rendering of the auth_login.php page
// page_bottom 		include/bottom_footer.php 	This hook allows you to add custom HTML to the bottom of the page, after the main layout table. 
// config_settings 	include/config_settings.php 	Allows you to extend the Cacti settings page to allow for custom tabs and global settings within them. 

// settings-> visual

function plugin_footer_install ()	{
//    api_plugin_register_hook('footer', 'config_arrays', 'footer_config_arrays', 'setup.php');
//    api_plugin_register_hook('footer', 'config_form','footer_config_form', 'setup.php');
    api_plugin_register_hook('footer', 'config_settings', 'footer_config_settings', 'setup.php');
    api_plugin_register_hook('footer', 'page_bottom', 'footer_page_bottom', 'setup.php');

}

function plugin_footer_uninstall ()	{
    // !!! smazat uploaded?
    db_execute ("delete from settings where name = 'footer_enable' limit 1");
    db_execute ("delete from settings where name = 'footer_content' limit 1");

}


function plugin_footer_version()	{
    global $config;
    $info = parse_ini_file($config['base_path'] . '/plugins/footer/INFO', true);
    return $info['info'];
}



function plugin_footer_check_config () {
	return true;
}



function footer_config_settings()    {
        global $settings;


    $temp = array(    
        'footer_display_header' => array(
                'friendly_name' => 'Footer',
                'method' => 'spacer',
        ),
        'footer_enable' => array(
                'friendly_name' => 'Display footer?',
                'description' => 'If checked footer will be displayed',
                'method' => 'checkbox',
                'default' => 'on',
        ),
        'footer_content' => array(
                'friendly_name' => 'Footer content, max 4000 chars',
                'description' => 'Alowed tags - b, i, br',
                'method' => 'textbox',
                'default' => 'You can change <b> footer </b> in:<br/> console -> settings -> visual<br/><br/>',
                'max_length' => '4000',
                'size' => '100'
        )
    );

    if (isset($settings["visual"])) {
	$settings["visual"] = array_merge($settings["visual"], $temp);
    }else {
	$settings["visual"] = $temp;
    }
}

function footer_page_bottom()	{

    if (read_config_option ('footer_enable') == 'on' )	{
	print '<div>' . read_config_option ('footer_content',TRUE) . '</div>';
    }
}

?>