<?php 

/*Add post thumbnail*/

function custom_theme_setup(){
	add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'custom_theme_setup');

/* CSS pour le Back */

function editorstyle(){
	add_editor_style('custome-editor-style.css');
}

add_action('after_setup_theme','editorstyle');


function admin_css() {

$admin_handle = 'admin_css';
$admin_stylesheet = get_template_directory_uri() . '/custome-editor-style.css';

wp_enqueue_style( $admin_handle, $admin_stylesheet );
}
add_action('admin_print_styles', 'admin_css', 11 );


function init_fields(){
	add_meta_box('id_poste', 'Poste au sein de l\'entreprise', 'id_poste');
}

function id_poste(){
	global $post;
	$custom = get_post_custom($post->ID);
	$id_poste = $custom["id_poste"][0];

	echo '<input type="text" size="70" value="';
		echo $id_poste;
	echo '" name="id_poste">';
	

}
add_action("admin_init", "init_fields");


function save_custom(){
	global $post;
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return $postID;
	}
}




/* OPTIONS SUPPLEMENTAIRES */

	//Ajout de l'onglet Option sup dans l'interface WP
	function menu_page(){
		add_menu_page('Page d\'accueil',
					   'Page d\'accueil',
					   'administrator',
					   'manage_options',
					   'options_page');
	}
	add_action('admin_menu', 'menu_page');

	//Champs à remplir dans l'onglet Options sup de l'interface
	function theme_options(){

		//Enregistrement du texte Description
		register_setting('my_theme','description');
		register_setting('my_theme','background');	
		register_setting('my_theme','text-color');
		register_setting('my_theme','header_link');
		register_setting('my_theme','header_link_v');

		//Enregistrement de l'image en banniere
		if(isset($_FILES["img"]) && $_FILES["img"]["error"]!=4)
			enregistrement_fichier($_FILES["img"], "img");

		//Enregistrement du logo en banniere
		if(isset($_FILES["logo"]) && $_FILES["logo"]["error"]!=4)
			enregistrement_fichier($_FILES["logo"], "logo");

		//Enregistrement du background en banniere
		if(isset($_FILES["bg"]) && $_FILES["bg"]["error"]!=4)
			enregistrement_fichier($_FILES["bg"], "bg");
	}
	add_action('admin_init','theme_options');

	//Config de l'onglet Options sup
	function options_page(){
		echo '<h1>Configuration de la page d\'accueil</h1>';
		echo '<form action="options.php" method="post" enctype="multipart/form-data">';

			settings_fields('my_theme');

			echo '<h3>Fond d\'écran de la bannière</h3>';
			echo '<label for="label_bg">';
				echo '<img id="banniere_bg" src="'.get_template_directory_uri().'/img/'.get_option("bg").'">';
				echo '<input id="label_bg" name="bg" type="file">';
			echo '</label>';

			echo '<h3>Logo du site</h3>';
			echo '<label for="label_logo">';
				echo '<img id="banniere_logo" src="'.get_template_directory_uri().'/img/'.get_option("logo").'">';
				echo '<input id="label_logo" name="logo" type="file">';
			echo '</label>';

			echo '<h3>Image de bannière</h3>';
			echo '<label for="label_img">';
				echo '<img id="banniere_img" src="'.get_template_directory_uri().'/img/'.get_option("img").'">';
				echo '<input id="label_img" name="img" type="file">';
			echo '</label>';

			echo '<label><h3>Description du site:</h3>'
					.'<textarea name="description" id="banniere_desc">'.get_option('description').'</textarea>'
				.'</label>';

			echo '<label class="color"><h3>Couleur de fond:</h3>'
					.'<input type="color" name="background" value='.get_option('background').'>'
				.'</label>';

			echo '<label class="color"><h3>Couleur du texte:</h3>'
					.'<input type="color" name="text-color" value='.get_option('text-color').'>'
				.'</label>';

			echo '<label class="color"><h3>Couleur des liens des menus:</h3>'
					.'<input type="color" name="header_link" value='.get_option('header_link').'>'
				.'</label>';

			echo '<label class="color"><h3>Couleur des liens ciblés (:hover) des menus:</h3>'
					.'<input type="color" name="header_link_v" value='.get_option('header_link_v').'>'
				.'</label>';

			echo '<input id="banniere_submit" value="Mettre à jour" type="submit">';

		echo '</form>';
	}

//Ajout de Calendrier image
function menu_page1(){
	add_menu_page('Calendrier',
		'Calendrier',
		'administrator',
		'Calendarovi',
		'options_page1');
}
add_action('admin_menu', 'menu_page1');

//Champs à remplir dans l'onglet Options sup de l'interface
function theme_options1(){

	//Enregistrement de l'image de event
	if(isset($_FILES["icone"]) && $_FILES["icone"]["error"]!=4)
		enregistrement_fichier($_FILES["icone"], "icone");


}
add_action('admin_init','theme_options1');

//Config de l'onglet Options sup
function options_page1(){
	echo '<h1>Configuration du Calendrier</h1>';
	echo '<form action="options.php" method="post" enctype="multipart/form-data">';

	settings_fields('my_theme');

	echo '<h3>Image des évènements</h3>';
	echo '<label for="icone">';
	echo '<img id="img" src="'.get_template_directory_uri().'/img/'.get_option("icone").'">';
	echo '<input id="icone" name="icone" type="file">';
	echo '</label>';
	echo '<input id="banniere_submit" value="Mettre à jour" type="submit">';
	echo '</form>';
}


/* UPLOAD D'UN FICHIER */
	function enregistrement_fichier($fichier, $nomBDD){
		global $wpdb;

		if($fichier["error"] > 0)
			echo "Error: " . $fichier["error"] . "<br>";
		else
		{
			$FileName = str_replace(" ","", $fichier["name"]);

			$url = plugin_dir_path( __FILE__ );
			move_uploaded_file($fichier["tmp_name"], $url .'/img/'. $fichier["name"]);
		}
		$results = $wpdb->get_results("SELECT COUNT(*) as nb FROM wp_options WHERE option_name='".$nomBDD."'");
		
		if($results[0]->nb == 0)
			$wpdb->query("INSERT INTO wp_options (option_name, option_value) VALUES ('".$nomBDD."', '".$FileName."')");
		else	
			$wpdb->query("UPDATE wp_options SET option_value='".$FileName."' WHERE option_name='".$nomBDD."'");
	}
