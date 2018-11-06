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
    api_plugin_register_hook('footer', 'login_after', 'footer_login_after', 'setup.php');
    api_plugin_register_hook('footer', 'global_settings_update', 'footer_global_settings_update', 'setup.php');

// nologo
//    db_execute("insert into settings (name,value) values ('plugin_footer_logo','')");



}

function plugin_footer_uninstall ()	{
    // !!! smazat uploaded?
    db_execute ("delete from settings where name like 'plugin_footer%'");

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
        'plugin_footer_display_header' => array(
                'friendly_name' => 'Footer',
                'method' => 'spacer',
        ),
        'plugin_footer_enable' => array(
                'friendly_name' => 'Display footer?',
                'description' => 'If checked footer will be displayed',
                'method' => 'checkbox',
                'default' => 'on',
        ),
        'plugin_footer_login_enable' => array(
                'friendly_name' => 'Display footer on login page?',
                'description' => 'If checked footer will be displayed on login page',
                'method' => 'checkbox',
                'default' => 'on',
        ),
/*
        'plugin_footer_logo' => array(
                'friendly_name' => ('Company logo'),
                'description' => __('You can add Company logo to the footer'),
                'method' => 'file'
                ),
*/
        'plugin_footer_content' => array(
                'friendly_name' => 'Footer content, max 4000 chars',
                'description' => 'Alowed tags - a, b, i, br',
                'method' => 'textarea',
                'textarea_rows' => '5',
                'textarea_cols' => '45',
                'max_length' => 4000,
                'default' => '<b>Footer plugin</b><br/>You can change me in Console -> settings -> visual<br/><i>You can upload file <b>logo.png</b> to /plugin/footer/uploaded/<br/>and I will display it</i> :-)'
        )
    );

    if (isset($settings["visual"])) {
	$settings["visual"] = array_merge($settings["visual"], $temp);
    }else {
	$settings["visual"] = $temp;
    }
}


function footer_global_settings_update()	{
    global $config;
    
    if (isset_request_var('plugin_footer_content')) {
        $content = substr(strip_tags(get_nfilter_request_var('plugin_footer_content'),'<i><a><b><br>'),0,3999);
        db_execute("UPDATE settings SET value='" . $content . "' where name='plugin_footer_content'");
	unset_request_var('plugin_footer_content');                
	set_config_option ('plugin_footer_content',$content);
    }
    
/*    
    // upload logo
    
    if (($_FILES['plugin_footer_logo']['tmp_name'] != 'none') && ($_FILES['plugin_footer_logo']['tmp_name'] != '') && $_FILES['plugin_footer_logo']['size'] < 250000) 	{
	$extension = strtolower(pathinfo($_FILES['plugin_footer_logo']['tmp_name'],PATHINFO_EXTENSION));
	$img = getimagesize($_FILES['plugin_footer_logo']['tmp_name']);
	if ($img && $img[0] < 300 && $img[1] < 300 && ($extension == 'jpg' || $extension == 'png' || $extension == 'gif'))	{
	    move_uploaded_file($_FILES['plugin_footer_logo']['tmp_name'], $config['base_path'] . '/plugins/footer/uploaded/logo.' . $extension);
    	    db_execute("UPDATE settings SET value='logo." . $extension . "' where name='plugin_footer_logo'");

	}
    }    
*/
}

function footer_page_bottom()	{

    if (read_config_option ('plugin_footer_enable') == 'on' )	{
	// exception for graph page, there is footer again
	if (get_nfilter_request_var('action') != "tree_content")	{
	    display_footer();
	}
//	var_export(debug_backtrace(), false);
    }
}


function footer_login_after()	{
    if (read_config_option ('plugin_footer_login_enable') == 'on' )	{
	display_footer();
    }
}


function display_footer()	{
    global $config;

    $selectedTheme = get_selected_theme();
    echo "<link type='text/css' href='" . $config['url_path'] . "plugins/footer/themes/common.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . $config['url_path'] . "plugins/footer/themes/" . $selectedTheme . ".css' rel='stylesheet'>";



    if (file_exists($config['base_path'] . '/plugins/footer/uploaded/logo.png'))	{
	$img = getimagesize($config['base_path'] . '/plugins/footer/uploaded/logo.png');
	if ($img && $img[0] < 300 && $img[1] < 300 && $img['mime'] == 'image/png')	{
	    echo "<img src='" . $config['url_path'] . "plugins/footer/uploaded/logo.png' id='footer_logo' />";
	}
    }
    print  read_config_option ('plugin_footer_content');
    

}

?>