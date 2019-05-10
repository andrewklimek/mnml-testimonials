<?php
/*
Plugin Name: Minimalist Testimonials
Description: a very light-weight testimonials plugin.  Shortcode [mnmltestimonials]
Version:     0.1
Plugin URI:  
Author:      Andrew J Klimek
Author URI:  https://github.com/andrewklimek
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Minimalist Testimonials is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free 
Software Foundation, either version 2 of the License, or any later version.

Minimalist Testimonials is distributed in the hope that it will be useful, but without 
any warranty; without even the implied warranty of merchantability or fitness for a 
particular purpose. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
Minimalist Testimonials. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

function mnmonials($a){
	
	$return = '';
	
	$num = !empty( $a['num'] ) ? $a['num'] : !empty( $a['list'] ) ? -1 : 20;
	
	$query = new WP_Query( array( 
		// 'category_name' => $a['cat'],
		'posts_per_page' => $num,
		// 'order' => $a['order'],
		'post_type' => 'testimonials'
	) );	
	
	// The Loop
	if ( $query->have_posts() ) {
		
		ob_start();
		
		// [mnmltestimonials list=1] will print all testimonials, no carousel		
		echo empty( $a['list'] ) ? '<div style="overflow-x:hidden"><div class="mnmonials-track">' : '<div class="mnmonials-archive">';
		
		while ( $query->have_posts() ) {
			
			$query->the_post();

			echo '<blockquote class="mnmonials"><div class="mnmonials-body">';
			
			the_content();
			
			echo '</div><footer class="pad">';
			
			if ( !empty( $a['thumb'] ) && has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			}
			
			the_title( '<cite class="mnmonials-by">', '</cite>' );
			
			// add custom meta under name
			$meta = get_post_meta(get_the_ID());
			if ( !empty($meta['testimonial_position']) || !empty($meta['testimonial_company']) ) {
				echo '<div class="mnmonials-title">';
				if ( !empty($meta['testimonial_position'][0]) ) echo  esc_html( $meta['testimonial_position'][0] );
				if ( !empty($meta['testimonial_position'][0]) && !empty($meta['testimonial_company'][0]) ) echo ", ";
				if ( !empty($meta['testimonial_company'][0]) ) echo  esc_html( $meta['testimonial_company'][0] );
				echo '</div>';
			}
			
			echo "</footer></blockquote>\n";
		
		}
		
		echo "</div>\n";
		
		if ( empty( $a['list'] ) ) :
								
		?>
		<style>
		.mnmonials-track{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-transition:-webkit-transform linear .5s;transition:-webkit-transform linear .5s;transition:transform linear .5s;transition:transform linear .5s, -webkit-transform linear .5s;position:relative;}
		.mnmonials{width:100%;-webkit-box-flex:0;-ms-flex:0 0 auto;flex:0 0 auto;}
		@media(min-width:30em){.mnmonials{width:50%;}}
		@media(min-width:50em){.mnmonials{width:33.33333%;}}
		</style>
		<script>(function(){function d(b){a=c.querySelector("[data-mnmonials]")||c.firstElementChild;a.removeAttribute("data-mnmonials");a=b?a.previousElementSibling:a.nextElementSibling||c.firstElementChild;c.style.transform="translateX(-"+a.offsetLeft+"px)";a.setAttribute("data-mnmonials","")}var c=document.querySelector(".mnmonials-track"),a,b;var e=setInterval(d,6E3);document.addEventListener("visibilitychange",function(){document.hidden?clearInterval(e):e=setInterval(d,6E3)});c.addEventListener("touchstart",
function(a){b=a.changedTouches[0].pageX});c.addEventListener("touchend",function(a){b-=a.changedTouches[0].pageX;-60>b?d(1):60<b?d():b=0;b&&clearInterval(e)})})();</script>
		</div>
		<?php
		
		/*** JS before minification
		var track = document.querySelector('.mnmonials-track'),
			activeAttribute = 'data-mnmonials',
			activeSlide, iid, touch;
		
		iid=setInterval(next,6e3);document.addEventListener('visibilitychange',function(){document.hidden?clearInterval(iid):(iid=setInterval(next,6e3));});
		
		track.addEventListener('touchstart', function(e){ touch=e.changedTouches[0].pageX; });
        track.addEventListener('touchend', function(e){ touch-=e.changedTouches[0].pageX; touch < -60 ? next(1) : touch > 60 ? next() : touch=0; touch && clearInterval(iid); });
        
        function next(prev) {
			activeSlide = track.querySelector('['+ activeAttribute +']') || track.firstElementChild;
			activeSlide.removeAttribute(activeAttribute);
			activeSlide = prev ? activeSlide.previousElementSibling : activeSlide.nextElementSibling || track.firstElementChild;
			track.style.transform = 'translateX(-'+ activeSlide.offsetLeft +'px)';
			activeSlide.setAttribute(activeAttribute,'');
		}
		***/
		
		/* For cancelling slider when tab is not active,
		This method is fewer characters for sure but i just hate the way it slides the instant you go back to the tab
		...also would need an " || iid=setTimeout(next,6e3);" for ie9 which doesn't have requestAnimationFrame (or just let ie9 not scroll!!)
		function q(){window.requestAnimationFrame(function(){iid=setTimeout(next,6e3);});} q();
		*/
		
		/*
		track.parentElement.addEventListener('scroll', clear );
		function clear(){
		    clearInterval(iid);
		    track.style.transform='';
		    track.parentElement.removeEventListener('scroll', clear );
		}
		*/
		
		endif;
		
		$return = ob_get_clean();
		
	}
	
	wp_reset_postdata();// Restore original Post Data
	
	return $return;
	
}
add_shortcode( 'mnmltestimonials', 'mnmonials');



add_action( 'add_meta_boxes_testimonials', 'mnmonials_add_custom_box' );

function mnmonials_add_custom_box() {
	add_meta_box(
		'mnmonials-id',               // Unique ID
		'Testimonial Info',           // Box title
		'mnmonials_inner_custom_box'  // Content callback
	);
}
/**
 * add the meta box.  As of 4.4, we don't need to do anything to actually write the meta on post save.
 * It is automatically by using name='meta_input[custom_meta_key]'
 * See https://github.com/WordPress/WordPress/blob/e6267dcf19f1309954e04b65a7fa8e9e2df5d0a4/wp-includes/post.php#L2825
 */
function mnmonials_inner_custom_box( $post ) {
	$values = get_post_meta( $post->ID );
	
	$fields = array(
		'Company'	=>	'',
		'Position'	=>	'',
	);
	print "<table class='form-table'><tbody>";
	
	foreach ( $fields as $label => $default ) {
		$field = 'testimonial_' . strtolower( str_replace( ' ', '_', $label ) );
		print "
	<tr>
		<th scope='row'><label for='mnmonials_{$field}'>{$label}</label></th>
		<td><input name='meta_input[{$field}]' type='text' id='mnmonials_{$field}' value='";
	print !empty( $values[ $field ][0] ) ? $values[ $field ][0] : $default;
	print "' class='regular-text ltr'></td>
	</tr>";
	}
	print "</tbody></table>";
}


function mnmonials_register_testimonials_post_type() {
    $args = array(
     'labels' => array(
       'name' => 'Testimonials',
       'singular_name' => 'Testimonial',
       ),
     'public' => true,
     'hierarchical' => false,
     'exclude_from_search' => true,
     'menu_icon' => 'dashicons-testimonial',
     // 'rewrite' => array ('slug' => 'testimonials'),
  );
    register_post_type( 'testimonials', $args );
}
add_action( 'init', 'mnmonials_register_testimonials_post_type' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'mnmonials_flush_rewrites' );

function mnmonials_flush_rewrites() {
	mnmonials_register_testimonials_post_type();
	flush_rewrite_rules();
}


/***********
JS I did’t use but may...

var track = document.querySelector('.quickcat-container');
var slides = track.querySelectorAll('article');
for ( var i=0; i < slides.length; ++i ){ slides[i].removeAttribute('data-slide-active'); }
document.querySelector('.post-12055').setAttribute('data-slide-active','');
track.style.transform = 'translateX(-'+ track.querySelector('[data-slide-active]').offsetLeft +'px)';

method 2
for ( var i=0; i < slides.length; ++i ){ slides[i].className=slides[i].className.replace(' slide-active',''); }
document.querySelector('.post-12055').className+=' slide-active';
track.style.transform = 'translateX(-'+ track.querySelector('.slide-active').offsetLeft +'px)';


CSS Example:
.mnmonials {
    padding: 0 1rem;
}
.mnmonials-body {
    background: #f5f5f5;
    padding: .5rem 2rem;
    position: relative;
}
.mnmonials-body::before {
    content: "\201C";
    line-height: 1;
    font-size: 7em;
    color: #f33733;
    position: absolute;
    top: -1rem;
    left: -1rem;
}
.mnmonials-by {
    font-weight: 700;
    font-style: normal;
    margin-top: 1.5rem;
}

.mnmonials-title {
    font-style: italic;
}
***********/
