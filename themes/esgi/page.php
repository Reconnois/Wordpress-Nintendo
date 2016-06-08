<?php
get_header();


if(is_active_sidebar('sidebar')){
	echo "<div id='sidebar'>";
		echo "<ul>";
		dynamic_sidebar('sidebar');	
		echo "</ul>";
	echo "</div>";
}
if(have_posts()){

	//Articles non mis en avant
	$query = new WP_Query( array( 'post__not_in' => get_option( 'sticky_posts' ) ) );
	while(have_posts()):
		the_post();
		?>
		<article class="post">
			<h1><?php the_title(); ?></h1>
			<div><?php the_content(); ?> </div>
			<ol>
				<?php wp_list_comments(); ?>
			</ol>
		</article>
		<?php
	endwhile;
}
else{
	?>
	<p>
	<?php _e('Sorry, no posts matched your criteria.');?>
	</p>
	<?php
}


get_footer();