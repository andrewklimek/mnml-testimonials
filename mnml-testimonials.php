<?php
/*
Plugin Name: Minimalist Testimonials
Description: a very light-weight testimonials plugin.  Shortcode [mnmltestimonials]
Version:     0.2.0
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


/*

TODO:
Add option to specify number of slides per page and maybe what pixel it switches to 1
Add option for seconds between transition
*/

function mnmonials($a){
	
	$return = '';
	
	$num = !empty( $a['num'] ) ? $a['num'] : ( !empty( $a['list'] ) ? -1 : 20 );
	
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
		.mnmonials-track{display:-ms-flexbox;display:flex;transition:transform linear .5s;position:relative}
		.mnmonials{width:100%;-ms-flex:0 0 auto;flex:none}
		/* @media(min-width:600px){.mnmonials{width:50%}} */
		@media(min-width:900px){.mnmonials{width:33.333%}}
		</style>
		<script>!function(){function e(e){e?--o:++o,0>o&&(o=0),r.style.transition="",r.style.transform="translateX(-"+100*o/(innerWidth<900?1:3)+"%)",o>a.length-4&&setTimeout(function(){o=0,r.style.transition="none",r.style.transform=""},3e3)}var t,n,r=document.querySelector(".mnmonials-track"),a=r.children,o=0;r.insertAdjacentHTML("beforeend",a[0].outerHTML+a[1].outerHTML+a[2].outerHTML),t=setInterval(e,6e3),document.addEventListener("visibilitychange",function(){document.hidden?clearInterval(t):t=setInterval(e,6e3)}),r.addEventListener("touchstart",function(e){n=e.changedTouches[0].pageX}),r.addEventListener("touchend",function(r){n-=r.changedTouches[0].pageX,-60>n?e(1):n>60?e():n=0,n&&clearInterval(t)})}();
		</script>
		</div>
		<?php
		
		/**** Before Minified
		
		var track = document.querySelector('.mnmonials-track')
			, slides = track.children
			// , slideNo = slides.length - 1
			, os = 0
			, iid, touch;
			
		
		// copy first 3 slides to end for infinite loop effect
		track.insertAdjacentHTML('beforeend', slides[0].outerHTML + slides[1].outerHTML + slides[2].outerHTML );
		
		iid=setInterval(next,6e3);
		
		document.addEventListener('visibilitychange',function(){document.hidden ? clearInterval(iid) : (iid=setInterval(next,6e3));});
		
		track.addEventListener('touchstart', function(e){ touch=e.changedTouches[0].pageX; });
        track.addEventListener('touchend', function(e){ touch-=e.changedTouches[0].pageX; touch < -60 ? next(1) : touch > 60 ? next() : touch=0; touch && clearInterval(iid); });
        
        function next(prev)
		{	
			prev?--os:++os;
			if(os<0)os=0;
		
			track.style.transition='';
			// track.style.transform = 'translateX(-'+ os * 100 / Math.min( 3, Math.floor(innerWidth / 300) ) +'%)';
			track.style.transform = 'translateX(-'+ os * 100 / (innerWidth < 900 ? 1 : 3) +'%)';
			
			if ( os > slides.length - 4 ) setTimeout( function(){	
				os = 0;
				track.style.transition = 'none';
				track.style.transform = '';
			}, 3e3 );
		}
		
		** End Before Minified */
		
		
		/*** cool but has a double pause when loops around again
		function next(prev)
		{	
			prev?--os:++os;
			if(os<0)os=0;
			if ( os > slides.length - 4 )
			{	
				os = 0;
				track.style.transition = 'none';
				track.style.transform = '';
			}
			else {
				track.style.transition='';
				track.style.transform = 'translateX(-'+ os * 100/3 +'%)';
			}
		}
		*/
		
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
 * 
 * I believe this is now broken as of 5.0.1, yet it still seems to be in the place referenced.
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
