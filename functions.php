<?php
defined( 'ABSPATH' ) || exit;

function wpbb_courses_project_mode( $mode ) { return 'elearning'; }
add_filter( 'wp_theme_project_mode', 'wpbb_courses_project_mode' );

function wpbb_courses_assets() {
    $theme = wp_get_theme();
    $manifest = get_stylesheet_directory() . '/dist/.vite/manifest.json';
    if ( ! is_readable( $manifest ) ) return;
    $data = json_decode( (string) file_get_contents( $manifest ), true );
    if ( ! is_array( $data ) ) return;
    if ( ! empty( $data['src/scss/public.scss']['file'] ) ) {
        wp_enqueue_style( 'wpbb-courses-app', get_stylesheet_directory_uri() . '/dist/' . ltrim( $data['src/scss/public.scss']['file'], '/' ), array(), $theme->get( 'Version' ) );
        if ( function_exists( 'wp_theme_sector_customizer_css' ) ) wp_add_inline_style( 'wpbb-courses-app', wp_theme_sector_customizer_css( '#4C45C6', '18px', '--sector-primary', '--sector-radius' ) );
    }
    if ( ! empty( $data['src/js/main.js']['file'] ) ) wp_enqueue_script( 'wpbb-courses-app', get_stylesheet_directory_uri() . '/dist/' . ltrim( $data['src/js/main.js']['file'], '/' ), array(), $theme->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'wpbb_courses_assets', 30 );

function wpbb_courses_dark_mode_bootstrap() { echo '<script>(function(){try{var m=localStorage.getItem("wpThemeMode");if(m==="dark"){document.documentElement.classList.add("is-dark-theme");document.documentElement.setAttribute("data-theme","dark");}}catch(e){}})();</script>'; }
add_action( 'wp_head', 'wpbb_courses_dark_mode_bootstrap', 1 );


function wpbb_courses_demo_profile( $profile ) {
    $assets = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/demo/';
    return array_merge( $profile, array(
        'id'=>'courses', 'name'=>__( 'E-Learning Academy', 'wp-bbtheme-child-elearning' ), 'commerce'=>false,
        'eyebrow'=>__( 'Learn in a clearer system', 'wp-bbtheme-child-elearning' ), 'hero_title'=>__( 'Courses built around progress, not just video.', 'wp-bbtheme-child-elearning' ), 'hero_text'=>__( 'Browse courses by topic and level, then move through lessons, video, PDF materials and practical quizzes in one WordPress learning experience.', 'wp-bbtheme-child-elearning' ),
        'hero_image'=>$assets . 'hero-photo.jpg', 'about_image'=>$assets . 'about-photo.jpg',
        'primary_label'=>__( 'Browse courses', 'wp-bbtheme-child-elearning' ), 'primary_url'=>'#finder',
        'secondary_label'=>__( 'Explore services', 'wp-bbtheme-child-elearning' ), 'secondary_url'=>wp_theme_demo_page_url( 'services' ),
        'services_eyebrow'=>__( 'What we do', 'wp-bbtheme-child-elearning' ), 'services_heading'=>__( 'A course platform with the learning materials close to the lesson.', 'wp-bbtheme-child-elearning' ),
        'about_eyebrow'=>__( 'Why choose us', 'wp-bbtheme-child-elearning' ), 'about_title'=>__( 'Video, resources and checks for understanding in one editable course system.', 'wp-bbtheme-child-elearning' ), 'about_text'=>__( 'A focused learning platform with clear outcomes, structured curriculum, lesson resources and meaningful knowledge checks.', 'wp-bbtheme-child-elearning' ),
        'industries_eyebrow'=>__( 'Built around your needs', 'wp-bbtheme-child-elearning' ), 'industries_heading'=>__( 'Courses for skills, teams, professional learning and self-paced study.', 'wp-bbtheme-child-elearning' ),
        'process_eyebrow'=>__( 'How it works', 'wp-bbtheme-child-elearning' ), 'process_heading'=>__( 'Choose a course, follow the curriculum and check understanding as you go.', 'wp-bbtheme-child-elearning' ), 'faq_heading'=>__( 'Course access, materials and learning questions answered clearly.', 'wp-bbtheme-child-elearning' ),
        'services'=>array(array( __( 'Structured curriculum', 'wp-bbtheme-child-elearning' ), __( 'Organise lessons into a clear sequence with duration and outcomes.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'Video lessons', 'wp-bbtheme-child-elearning' ), __( 'Use embeddable lesson video alongside explanatory content.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'PDF materials', 'wp-bbtheme-child-elearning' ), __( 'Attach worksheets, reference PDFs and downloadable resources.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'Quizzes & tests', 'wp-bbtheme-child-elearning' ), __( 'Add multiple-choice knowledge checks with instant scoring.', 'wp-bbtheme-child-elearning' ) )), 'industries'=>array(array( __( 'Professional skills', 'wp-bbtheme-child-elearning' ), __( 'Practical courses built around workplace outcomes.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'Creative learning', 'wp-bbtheme-child-elearning' ), __( 'Video, examples and exercises for creative subjects.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'Team training', 'wp-bbtheme-child-elearning' ), __( 'Repeatable learning material for internal programmes.', 'wp-bbtheme-child-elearning' ) ),
array( __( 'Self-paced study', 'wp-bbtheme-child-elearning' ), __( 'Clear progress through modules without unnecessary complexity.', 'wp-bbtheme-child-elearning' ) )), 'stats'=>array(array( '6', __( 'Active courses', 'wp-bbtheme-child-elearning' ) ),
array( '18', __( 'Lessons available', 'wp-bbtheme-child-elearning' ) ),
array( '12', __( 'Quiz questions', 'wp-bbtheme-child-elearning' ) ),
array( '100%', __( 'Gutenberg editable', 'wp-bbtheme-child-elearning' ) )), 'process'=>array(array( '01', __( 'Choose', 'wp-bbtheme-child-elearning' ), __( 'Filter by category, level and course length.', 'wp-bbtheme-child-elearning' ) ),
array( '02', __( 'Learn', 'wp-bbtheme-child-elearning' ), __( 'Move through video, reading and downloadable materials.', 'wp-bbtheme-child-elearning' ) ),
array( '03', __( 'Check', 'wp-bbtheme-child-elearning' ), __( 'Use quizzes to reinforce the important ideas.', 'wp-bbtheme-child-elearning' ) )),
        'cta_title'=>__( 'Turn expertise into a course people can actually finish.', 'wp-bbtheme-child-elearning' ), 'cta_text'=>__( 'Use the course, lesson, material and quiz content model as a flexible foundation for training or online education.', 'wp-bbtheme-child-elearning' ), 'footer_text'=>__( 'Structured online learning with courses, lesson media, downloadable resources and knowledge checks.', 'wp-bbtheme-child-elearning' ),
        'page_labels'=>array('about'=>__( 'About', 'wp-bbtheme-child-elearning' ),'services'=>__( 'Services', 'wp-bbtheme-child-elearning' ),'industries'=>__( 'Solutions', 'wp-bbtheme-child-elearning' ),'contact'=>__( 'Contact', 'wp-bbtheme-child-elearning' ),'blog'=>__( 'Insights', 'wp-bbtheme-child-elearning' )),
        'palette'=>array('theme_brand_color'=>'#4C45C6','theme_accent_color'=>'#1E8C78','theme_background_color'=>'#f7f8fb','theme_surface_color'=>'#ffffff','theme_border_color'=>'#dfe4ee','theme_radius'=>'22px')
    ) );
}
add_filter( 'wp_theme_demo_profile', 'wpbb_courses_demo_profile', 20 );


function wpbb_courses_pattern_markup( $name ) {
    $path = get_stylesheet_directory() . '/patterns/' . sanitize_file_name( $name ) . '.php';
    if ( ! is_readable( $path ) ) return '';
    ob_start(); include $path; return trim( (string) ob_get_clean() );
}

function wpbb_courses_extra_home_sections( $content, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return $content;
    return $content . wpbb_courses_pattern_markup( 'sector-proof' );
}
add_filter( 'wp_theme_demo_extra_home_sections', 'wpbb_courses_extra_home_sections', 25, 2 );

function wpbb_courses_blog_profile( $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return $profile;
    $profile['blog_eyebrow'] = __( 'Insights', 'wp-bbtheme-child-elearning' );
    $profile['blog_archive_title'] = __( 'Learning notes, study guides and course updates.', 'wp-bbtheme-child-elearning' );
    $profile['blog_archive_intro'] = __( 'Useful articles that support the learning journey between lessons.', 'wp-bbtheme-child-elearning' );
    return $profile;
}
add_filter( 'wp_theme_demo_profile', 'wpbb_courses_blog_profile', 90 );


function wpbb_courses_demo_attachment( $filename, $title ) {
    $slug = sanitize_title( pathinfo( $filename, PATHINFO_FILENAME ) );
    $existing = get_page_by_path( 'wpbb-courses-' . $slug, OBJECT, 'attachment' );
    if ( $existing ) return $existing->ID;
    $source = get_stylesheet_directory() . '/assets/img/demo/' . basename( $filename );
    if ( ! is_readable( $source ) ) return 0;
    $uploads = wp_upload_dir(); $dir = trailingslashit( $uploads['basedir'] ) . 'wpbb-courses'; wp_mkdir_p( $dir );
    $target = $dir . '/' . basename( $filename ); if ( ! file_exists( $target ) ) copy( $source, $target );
    $filetype = wp_check_filetype( $target );
    $id = wp_insert_attachment( array( 'post_mime_type'=>$filetype['type'] ?: 'image/jpeg', 'post_title'=>$title, 'post_name'=>'wpbb-courses-' . $slug, 'post_status'=>'inherit' ), $target );
    if ( $id && ! is_wp_error( $id ) ) {
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) require_once ABSPATH . 'wp-admin/includes/image.php';
        $meta = wp_generate_attachment_metadata( $id, $target ); if ( $meta ) wp_update_attachment_metadata( $id, $meta ); update_post_meta( $id, '_wp_attachment_image_alt', $title );
        return (int) $id;
    }
    return 0;
}

function wpbb_courses_register_directory() {
    register_post_type( 'course', array(
        'labels'=>array('name'=>__( 'Courses', 'wp-bbtheme-child-elearning' ),'singular_name'=>__( 'Course', 'wp-bbtheme-child-elearning' ),'add_new_item'=>__( 'Add Course', 'wp-bbtheme-child-elearning' )),
        'public'=>true,'show_in_rest'=>true,'has_archive'=>'courses','rewrite'=>array('slug'=>'courses'),'menu_icon'=>'dashicons-welcome-learn-more','supports'=>array('title','editor','excerpt','thumbnail','page-attributes')
    ) );
    register_taxonomy( 'course_category', 'course', array( 'label'=>__( 'Course categories', 'wp-bbtheme-child-elearning' ), 'public'=>true, 'show_in_rest'=>true, 'hierarchical'=>true, 'rewrite'=>array('slug'=>'course-category') ) ); register_taxonomy( 'course_level', 'course', array( 'label'=>__( 'Course levels', 'wp-bbtheme-child-elearning' ), 'public'=>true, 'show_in_rest'=>true, 'hierarchical'=>true, 'rewrite'=>array('slug'=>'course-level') ) );
}
add_action( 'init', 'wpbb_courses_register_directory', 12 );

function wpbb_courses_meta_fields() { return array('price'=>__( 'Course price', 'wp-bbtheme-child-elearning' ),'duration_hours'=>__( 'Study hours', 'wp-bbtheme-child-elearning' ),'lesson_count'=>__( 'Lessons', 'wp-bbtheme-child-elearning' ),'instructor'=>__( 'Instructor', 'wp-bbtheme-child-elearning' ),'rating'=>__( 'Learner rating', 'wp-bbtheme-child-elearning' )); }
function wpbb_courses_meta_box() { add_meta_box( 'wpbb-courses-details', __( 'Course details', 'wp-bbtheme-child-elearning' ), 'wpbb_courses_meta_box_render', 'course', 'normal', 'high' ); }
add_action( 'add_meta_boxes', 'wpbb_courses_meta_box' );
function wpbb_courses_meta_box_render( $post ) {
    wp_nonce_field( 'wpbb_courses_save', 'wpbb_courses_nonce' ); echo '<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px">';
    foreach ( wpbb_courses_meta_fields() as $key=>$label ) { $value=get_post_meta($post->ID,'_courses_'.$key,true); echo '<label><strong>'.esc_html($label).'</strong><input class="widefat" type="text" name="wpbb_courses['.esc_attr($key).']" value="'.esc_attr($value).'"></label>'; } echo '</div>';
}
function wpbb_courses_save_meta( $post_id ) {
    if ( empty($_POST['wpbb_courses_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpbb_courses_nonce'])),'wpbb_courses_save') || !current_user_can('edit_post',$post_id) ) return;
    $values=isset($_POST['wpbb_courses'])&&is_array($_POST['wpbb_courses'])?wp_unslash($_POST['wpbb_courses']):array(); foreach(wpbb_courses_meta_fields() as $key=>$label) update_post_meta($post_id,'_courses_'.$key,sanitize_text_field($values[$key]??''));
}
add_action( 'save_post_course', 'wpbb_courses_save_meta' );

function wpbb_courses_directory_configs( $configs ) {
    $configs['courses'] = array(
      'post_type'=>'course','eyebrow'=>__( 'Course catalogue', 'wp-bbtheme-child-elearning' ),'title'=>__( 'Find a course that matches the skill you want to build.', 'wp-bbtheme-child-elearning' ),'intro'=>__( 'Filter by subject, level and estimated study time.', 'wp-bbtheme-child-elearning' ),'keyword_label'=>__( 'Search courses', 'wp-bbtheme-child-elearning' ),'keyword_placeholder'=>__( 'Design, Python, leadership…', 'wp-bbtheme-child-elearning' ),'button_label'=>__( 'Find courses', 'wp-bbtheme-child-elearning' ),'results_label'=>__( 'courses to explore', 'wp-bbtheme-child-elearning' ),'limit'=>8,'default_sort'=>'featured',
      'filters'=>array(array('type'=>'taxonomy','key'=>'category','label'=>__( 'Category', 'wp-bbtheme-child-elearning' ),'taxonomy'=>'course_category','all_label'=>'Any category'),array('type'=>'taxonomy','key'=>'level','label'=>__( 'Level', 'wp-bbtheme-child-elearning' ),'taxonomy'=>'course_level','all_label'=>'Any level'),array('type'=>'meta_max','key'=>'max_hours','label'=>__( 'Max study hours', 'wp-bbtheme-child-elearning' ),'meta_key'=>'_courses_duration_hours','placeholder'=>'Any','step'=>1),array('type'=>'meta_max','key'=>'max_price','label'=>__( 'Max price', 'wp-bbtheme-child-elearning' ),'meta_key'=>'_courses_price','placeholder'=>'Any','step'=>10)),'sorts'=>array('featured'=>array('label'=>__( 'Recommended', 'wp-bbtheme-child-elearning' ),'orderby'=>'menu_order','order'=>'ASC'),'rating'=>array('label'=>__( 'Highest rated', 'wp-bbtheme-child-elearning' ),'orderby'=>'meta_value_num','order'=>'DESC','meta_key'=>'_courses_rating'),'duration'=>array('label'=>__( 'Shortest first', 'wp-bbtheme-child-elearning' ),'orderby'=>'meta_value_num','order'=>'ASC','meta_key'=>'_courses_duration_hours')),'card_taxonomies'=>array('course_category','course_level'),'card_meta'=>array(array('key'=>'_courses_price','label'=>__( 'Price', 'wp-bbtheme-child-elearning' ),'format'=>'money','currency'=>'£'),array('key'=>'_courses_duration_hours','label'=>__( 'Study time', 'wp-bbtheme-child-elearning' ),'suffix'=>'h'),array('key'=>'_courses_lesson_count','label'=>__( 'Lessons', 'wp-bbtheme-child-elearning' )),array('key'=>'_courses_rating','label'=>__( 'Rating', 'wp-bbtheme-child-elearning' ),'suffix'=>'/5')),'card_button'=>__( 'View course', 'wp-bbtheme-child-elearning' )
    ); return $configs;
}
add_filter( 'wp_theme_sector_directory_configs', 'wpbb_courses_directory_configs' );

function wpbb_courses_seed_directory( $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return;
    $rows=array(array('title'=>'Product Design Foundations','slug'=>'product-design-foundations','excerpt'=>'Research, flows, prototypes and critique for useful digital products.','content'=>'Research, flows, prototypes and critique for useful digital products.','terms'=>array('course_category'=>'Design','course_level'=>'Beginner'),'meta'=>array('price'=>'79','duration_hours'=>'8','lesson_count'=>'6','instructor'=>'Maya Chen','rating'=>'4.9'),'image'=>'item-1.jpg'),array('title'=>'Python for Practical Automation','slug'=>'python-practical-automation','excerpt'=>'Build small scripts that remove repetitive work.','content'=>'Build small scripts that remove repetitive work.','terms'=>array('course_category'=>'Development','course_level'=>'Beginner'),'meta'=>array('price'=>'99','duration_hours'=>'10','lesson_count'=>'7','instructor'=>'Alex Morgan','rating'=>'4.8'),'image'=>'item-2.jpg'),array('title'=>'Digital Marketing Systems','slug'=>'digital-marketing-systems','excerpt'=>'Plan campaigns, measurement and reusable content workflows.','content'=>'Plan campaigns, measurement and reusable content workflows.','terms'=>array('course_category'=>'Marketing','course_level'=>'Intermediate'),'meta'=>array('price'=>'119','duration_hours'=>'7','lesson_count'=>'5','instructor'=>'Sara Ahmed','rating'=>'4.7'),'image'=>'item-3.jpg'),array('title'=>'Leadership for Small Teams','slug'=>'leadership-small-teams','excerpt'=>'Clear expectations, useful feedback and better team rhythms.','content'=>'Clear expectations, useful feedback and better team rhythms.','terms'=>array('course_category'=>'Leadership','course_level'=>'Intermediate'),'meta'=>array('price'=>'89','duration_hours'=>'6','lesson_count'=>'5','instructor'=>'Jon Bell','rating'=>'4.8'),'image'=>'item-4.jpg'),array('title'=>'Excel Analysis Essentials','slug'=>'excel-analysis-essentials','excerpt'=>'Clean data, useful formulas and decision-ready summaries.','content'=>'Clean data, useful formulas and decision-ready summaries.','terms'=>array('course_category'=>'Business','course_level'=>'Beginner'),'meta'=>array('price'=>'59','duration_hours'=>'5','lesson_count'=>'4','instructor'=>'Elena Rossi','rating'=>'4.6'),'image'=>'item-5.jpg'),array('title'=>'UX Research in Practice','slug'=>'ux-research-practice','excerpt'=>'Plan interviews, synthesize findings and turn insight into decisions.','content'=>'Plan interviews, synthesize findings and turn insight into decisions.','terms'=>array('course_category'=>'Design','course_level'=>'Advanced'),'meta'=>array('price'=>'139','duration_hours'=>'9','lesson_count'=>'6','instructor'=>'Maya Chen','rating'=>'4.9'),'image'=>'item-6.jpg'));
    foreach($rows as $i=>$row){
      foreach($row['terms'] as $tax=>$term) if(taxonomy_exists($tax)&&!term_exists($term,$tax)) wp_insert_term($term,$tax);
      $existing=get_page_by_path($row['slug'],OBJECT,'course'); $args=array('post_type'=>'course','post_status'=>'publish','post_title'=>$row['title'],'post_name'=>$row['slug'],'menu_order'=>$i,'post_excerpt'=>$row['excerpt'],'post_content'=>'<!-- wp:paragraph --><p>'.esc_html($row['content']).'</p><!-- /wp:paragraph -->');
      if($existing){$args['ID']=$existing->ID;$id=wp_update_post($args);}else{$id=wp_insert_post($args);} if(!$id||is_wp_error($id))continue;
      foreach($row['terms'] as $tax=>$term)wp_set_object_terms($id,$term,$tax); foreach($row['meta'] as $key=>$value)update_post_meta($id,'_courses_'.$key,$value); $img=wpbb_courses_demo_attachment($row['image'],$row['title']); if($img)set_post_thumbnail($id,$img); update_post_meta($id,'_wp_theme_demo_course',1);
    }
}
add_action( 'wp_theme_seed_sector_pages', 'wpbb_courses_seed_directory', 25 );

function wpbb_courses_after_hero_finder( $content, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return $content;
    return $content . '<!-- wp:group {"className":"wp-theme-section-shell wpbb-courses-finder-section","layout":{"type":"default"}} --><div class="wp-block-group wp-theme-section-shell wpbb-courses-finder-section"><!-- wp:wpbb/row {"containerClass":"container"} --><!-- wp:wpbb/column {"xs":12} --><!-- wp:wpbb/sector-finder {"context":"courses","limit":8} /--><!-- /wp:wpbb/column --><!-- /wp:wpbb/row --></div><!-- /wp:group -->';
}
add_filter( 'wp_theme_demo_after_hero_sections', 'wpbb_courses_after_hero_finder', 20, 2 );

function wpbb_courses_navigation( $items, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return $items;
    array_splice( $items, 1, 0, array( array('key'=>'course','title'=>__( 'Courses', 'wp-bbtheme-child-elearning' ),'type'=>'post_type_archive','object'=>'course','locations'=>array('header','footer')) ) ); return $items;
}
add_filter( 'wp_theme_demo_navigation_items', 'wpbb_courses_navigation', 20, 2 );

function wpbb_courses_header_search_types( $types ) { if(post_type_exists('course'))$types[]='course'; return array_values(array_unique($types)); }
add_filter( 'wp_theme_header_search_post_types', 'wpbb_courses_header_search_types' );

function wpbb_courses_single_content( $content ) {
    if ( !is_singular('course') || !in_the_loop() || !is_main_query() ) return $content; $id=get_the_ID(); $image=get_the_post_thumbnail_url($id,'large'); $gallery=function_exists('wp_theme_item_gallery_single_markup')?wp_theme_item_gallery_single_markup($id):'';
    $facts=''; foreach(wpbb_courses_meta_fields() as $key=>$label){$value=get_post_meta($id,'_courses_'.$key,true);if(''!==trim((string)$value))$facts.='<div><small>'.esc_html($label).'</small><strong>'.esc_html($value).'</strong></div>';}
    $html='<section class="wpbb-sector-single"><div class="container"><div class="wpbb-sector-single__hero"><div class="wpbb-sector-single__media">'.($gallery?:($image?'<img src="'.esc_url($image).'" alt="'.esc_attr(get_the_title()).'">':'')).'</div><div><p class="wp-theme-sector-eyebrow">'.esc_html('Course').'</p><h1>'.esc_html(get_the_title()).'</h1><p class="wp-theme-sector-lead">'.esc_html(get_the_excerpt()).'</p><div class="wpbb-sector-single__facts">'.$facts.'</div></div></div><div class="wpbb-sector-single__content">'.$content.'</div>';
    if(function_exists('wpbb_courses_request_form'))$html.=wpbb_courses_request_form($id); return $html.'</div></section>';
}
add_filter( 'the_content', 'wpbb_courses_single_content', 25 );

function wpbb_courses_polylang_post_types( $types, $settings ) { $types['course']='course'; return $types; }
add_filter( 'pll_get_post_types', 'wpbb_courses_polylang_post_types', 10, 2 );
function wpbb_courses_pll_course_category( $tax, $settings ) { $tax['course_category']='course_category'; return $tax; }
add_filter( 'pll_get_taxonomies', 'wpbb_courses_pll_course_category', 10, 2 );
function wpbb_courses_pll_course_level( $tax, $settings ) { $tax['course_level']='course_level'; return $tax; }
add_filter( 'pll_get_taxonomies', 'wpbb_courses_pll_course_level', 10, 2 );

function wpbb_courses_mega_menu( $definitions, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'courses' ) return $definitions; $archive=get_post_type_archive_link('course')?:home_url('/courses/');
    $definitions['course']=array('title'=>__( 'Courses navigation', 'wp-bbtheme-child-elearning' ),'target_key'=>'course','eyebrow'=>__( 'Courses', 'wp-bbtheme-child-elearning' ),'heading'=>__( 'Choose what you want to learn next.', 'wp-bbtheme-child-elearning' ),'intro'=>__( 'Browse by subject, level or study time, then open the curriculum.', 'wp-bbtheme-child-elearning' ),'columns'=>array(
      array('title'=>__( 'Explore', 'wp-bbtheme-child-elearning' ),'links'=>array(array(__( 'Courses', 'wp-bbtheme-child-elearning' ),__( 'Filter by subject, level and estimated study time.', 'wp-bbtheme-child-elearning' ),$archive),array(__( 'Services', 'wp-bbtheme-child-elearning' ),__( 'A course platform with the learning materials close to the lesson.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('services')),array(__( 'Solutions', 'wp-bbtheme-child-elearning' ),__( 'Courses for skills, teams, professional learning and self-paced study.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('industries')))),
      array('title'=>__( 'Plan', 'wp-bbtheme-child-elearning' ),'links'=>array(array(__( 'How it works', 'wp-bbtheme-child-elearning' ),__( 'Choose a course, follow the curriculum and check understanding as you go.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('services')),array(__( 'About', 'wp-bbtheme-child-elearning' ),__( 'A focused learning platform with clear outcomes, structured curriculum, lesson resources and meaningful knowledge checks.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('about')),array(__( 'Contact', 'wp-bbtheme-child-elearning' ),__( 'Talk to the team about the next step.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('contact')))),
      array('title'=>__( 'Useful', 'wp-bbtheme-child-elearning' ),'links'=>array(array(__( 'Insights', 'wp-bbtheme-child-elearning' ),__( 'Useful articles that support the learning journey between lessons.', 'wp-bbtheme-child-elearning' ),get_permalink(get_option('page_for_posts'))?:home_url('/blog/')),array(__( 'Search', 'wp-bbtheme-child-elearning' ),__( 'Use the live finder to narrow the catalogue.', 'wp-bbtheme-child-elearning' ),$archive),array(__( 'Enquire', 'wp-bbtheme-child-elearning' ),__( 'Send the details needed for a useful response.', 'wp-bbtheme-child-elearning' ),wp_theme_demo_page_url('contact'))))
    )); return $definitions;
}
add_filter('wp_theme_demo_mega_menu_definitions','wpbb_courses_mega_menu',20,2);

function wpbb_courses_register_learning_content() {
    register_post_type('course_lesson',array('labels'=>array('name'=>__( 'Lessons', 'wp-bbtheme-child-elearning' ),'singular_name'=>__( 'Lesson', 'wp-bbtheme-child-elearning' )),'public'=>true,'show_in_rest'=>true,'has_archive'=>false,'rewrite'=>array('slug'=>'lesson'),'menu_icon'=>'dashicons-video-alt3','supports'=>array('title','editor','excerpt','thumbnail','page-attributes')));
    register_post_type('course_quiz',array('labels'=>array('name'=>__( 'Quizzes', 'wp-bbtheme-child-elearning' ),'singular_name'=>__( 'Quiz Question', 'wp-bbtheme-child-elearning' )),'public'=>false,'show_ui'=>true,'show_in_menu'=>'edit.php?post_type=course','supports'=>array('title','page-attributes')));
}
add_action('init','wpbb_courses_register_learning_content',13);
function wpbb_courses_lesson_fields(){return array('course_id'=>__( 'Course ID', 'wp-bbtheme-child-elearning' ),'video_url'=>__( 'Video URL', 'wp-bbtheme-child-elearning' ),'material_url'=>__( 'PDF / material URL', 'wp-bbtheme-child-elearning' ),'duration'=>__( 'Lesson duration', 'wp-bbtheme-child-elearning' ));}
function wpbb_courses_lesson_box(){add_meta_box('wpbb-course-lesson',__( 'Lesson media & course', 'wp-bbtheme-child-elearning' ),'wpbb_courses_lesson_box_render','course_lesson','normal','high');add_meta_box('wpbb-course-quiz',__( 'Quiz question', 'wp-bbtheme-child-elearning' ),'wpbb_courses_quiz_box_render','course_quiz','normal','high');} add_action('add_meta_boxes','wpbb_courses_lesson_box');
function wpbb_courses_lesson_box_render($post){wp_nonce_field('wpbb_courses_learning','wpbb_courses_learning_nonce');echo '<div style="display:grid;gap:12px">';foreach(wpbb_courses_lesson_fields() as $key=>$label){$v=get_post_meta($post->ID,'_course_'.$key,true);echo '<label><strong>'.esc_html($label).'</strong><input class="widefat" name="wpbb_course_lesson['.esc_attr($key).']" value="'.esc_attr($v).'"></label>';}echo '</div>';}
function wpbb_courses_quiz_box_render($post){wp_nonce_field('wpbb_courses_quiz','wpbb_courses_quiz_nonce');$fields=array('course_id'=>'Course ID','question'=>'Question','choice_a'=>'Choice A','choice_b'=>'Choice B','choice_c'=>'Choice C','choice_d'=>'Choice D','correct'=>'Correct letter (a/b/c/d)','explanation'=>'Explanation');echo '<div style="display:grid;gap:12px">';foreach($fields as $key=>$label){$v=get_post_meta($post->ID,'_quiz_'.$key,true);echo '<label><strong>'.esc_html($label).'</strong><input class="widefat" name="wpbb_course_quiz['.esc_attr($key).']" value="'.esc_attr($v).'"></label>';}echo '</div>';}
function wpbb_courses_save_lesson($post_id){if(empty($_POST['wpbb_courses_learning_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpbb_courses_learning_nonce'])),'wpbb_courses_learning')||!current_user_can('edit_post',$post_id))return;$v=isset($_POST['wpbb_course_lesson'])&&is_array($_POST['wpbb_course_lesson'])?wp_unslash($_POST['wpbb_course_lesson']):array();foreach(wpbb_courses_lesson_fields() as $key=>$label)update_post_meta($post_id,'_course_'.$key,esc_url_raw($v[$key]??'')?:sanitize_text_field($v[$key]??''));} add_action('save_post_course_lesson','wpbb_courses_save_lesson');
function wpbb_courses_save_quiz($post_id){if(empty($_POST['wpbb_courses_quiz_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpbb_courses_quiz_nonce'])),'wpbb_courses_quiz')||!current_user_can('edit_post',$post_id))return;$v=isset($_POST['wpbb_course_quiz'])&&is_array($_POST['wpbb_course_quiz'])?wp_unslash($_POST['wpbb_course_quiz']):array();foreach(array('course_id','question','choice_a','choice_b','choice_c','choice_d','correct','explanation') as $key)update_post_meta($post_id,'_quiz_'.$key,sanitize_text_field($v[$key]??''));} add_action('save_post_course_quiz','wpbb_courses_save_quiz');

function wpbb_courses_seed_learning( $profile ) {
 if(($profile['id']??'')!=='courses')return;
 $courses=get_posts(array('post_type'=>'course','post_status'=>'publish','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC')); if(!$courses)return;
 $video='https://www.youtube.com/watch?v=aqz-KE-bpKQ'; $pdf='https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
 foreach($courses as $ci=>$course){
   for($i=1;$i<=3;$i++){$slug=$course->post_name.'-lesson-'.$i;$existing=get_page_by_path($slug,OBJECT,'course_lesson');$args=array('post_type'=>'course_lesson','post_status'=>'publish','post_title'=>sprintf(__( 'Lesson %1$d — %2$s', 'wp-bbtheme-child-elearning' ),$i,$course->post_title),'post_name'=>$slug,'menu_order'=>$i,'post_excerpt'=>__( 'A focused lesson with video, notes and a downloadable reference.', 'wp-bbtheme-child-elearning' ),'post_content'=>'<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">'.esc_html(__( 'Lesson notes', 'wp-bbtheme-child-elearning' )).'</h2><!-- /wp:heading --><!-- wp:paragraph --><p>'.esc_html(__( 'This lesson combines teaching content, practical examples, exercises and supporting references.', 'wp-bbtheme-child-elearning' )).'</p><!-- /wp:paragraph -->');if($existing){$args['ID']=$existing->ID;$id=wp_update_post($args);}else{$id=wp_insert_post($args);}if($id&&!is_wp_error($id)){update_post_meta($id,'_course_course_id',$course->ID);update_post_meta($id,'_course_video_url',$video);update_post_meta($id,'_course_material_url',$pdf);update_post_meta($id,'_course_duration',(12+$i*4).' min');}}
   $questions=array(array(__( 'Which approach best supports durable learning?', 'wp-bbtheme-child-elearning' ),array(__( 'Clear outcomes and practice', 'wp-bbtheme-child-elearning' ),__( 'More decoration', 'wp-bbtheme-child-elearning' ),__( 'Longer pages only', 'wp-bbtheme-child-elearning' ),__( 'Fewer examples', 'wp-bbtheme-child-elearning' )),'a'),array(__( 'Where should supporting resources appear?', 'wp-bbtheme-child-elearning' ),array(__( 'Close to the relevant lesson', 'wp-bbtheme-child-elearning' ),__( 'Hidden in the footer', 'wp-bbtheme-child-elearning' ),__( 'Only by email', 'wp-bbtheme-child-elearning' ),__( 'Nowhere', 'wp-bbtheme-child-elearning' )),'a'));
   foreach($questions as $qi=>$question){$slug=$course->post_name.'-quiz-'.($qi+1);$existing=get_page_by_path($slug,OBJECT,'course_quiz');$args=array('post_type'=>'course_quiz','post_status'=>'publish','post_title'=>$course->post_title.' quiz '.($qi+1),'post_name'=>$slug,'menu_order'=>$qi);if($existing){$args['ID']=$existing->ID;$qid=wp_update_post($args);}else{$qid=wp_insert_post($args);}if($qid&&!is_wp_error($qid)){update_post_meta($qid,'_quiz_course_id',$course->ID);update_post_meta($qid,'_quiz_question',$question[0]);foreach(array('a','b','c','d') as $ix=>$letter)update_post_meta($qid,'_quiz_choice_'.$letter,$question[1][$ix]);update_post_meta($qid,'_quiz_correct',$question[2]);update_post_meta($qid,'_quiz_explanation',__( 'The best answer keeps the learning action close to the objective and supporting material.', 'wp-bbtheme-child-elearning' ));}}
 }
}
add_action('wp_theme_seed_sector_pages','wpbb_courses_seed_learning',35);

function wpbb_courses_curriculum_markup($course_id){
 $lessons=get_posts(array('post_type'=>'course_lesson','post_status'=>'publish','numberposts'=>-1,'meta_key'=>'_course_course_id','meta_value'=>$course_id,'orderby'=>'menu_order','order'=>'ASC')); $out='<section class="wpbb-course-curriculum"><p class="wp-theme-sector-eyebrow">'.esc_html(__( 'Curriculum', 'wp-bbtheme-child-elearning' )).'</p><h2>'.esc_html(__( 'Lessons, video and materials', 'wp-bbtheme-child-elearning' )).'</h2><div class="wpbb-course-lessons">';foreach($lessons as $lesson){$video=get_post_meta($lesson->ID,'_course_video_url',true);$material=get_post_meta($lesson->ID,'_course_material_url',true);$duration=get_post_meta($lesson->ID,'_course_duration',true);$out.='<article class="wpbb-sector-proof-card"><h3><a href="'.esc_url(get_permalink($lesson)).'">'.esc_html($lesson->post_title).'</a></h3><p>'.esc_html($duration).'</p><div class="d-flex gap-2 flex-wrap">'.($video?'<button class="btn btn-outline-primary wpbb-course-video-trigger" type="button" data-course-video-modal data-video-url="'.esc_url($video).'" data-video-title="'.esc_attr($lesson->post_title).'">'.esc_html(__( 'Watch video', 'wp-bbtheme-child-elearning' )).'</button>':'').($material?'<a class="btn btn-outline-primary" href="'.esc_url($material).'" target="_blank" rel="noopener">'.esc_html(__( 'PDF material', 'wp-bbtheme-child-elearning' )).'</a>':'').'</div></article>';}$out.='</div></section>';return $out;
}
function wpbb_courses_quiz_markup($course_id){
 $questions=get_posts(array('post_type'=>'course_quiz','post_status'=>'publish','numberposts'=>-1,'meta_key'=>'_quiz_course_id','meta_value'=>$course_id,'orderby'=>'menu_order','order'=>'ASC'));if(!$questions)return'';$score=null;if('POST'===($_SERVER['REQUEST_METHOD']??'')&&absint($_POST['wpbb_quiz_course']??0)===$course_id&&isset($_POST['wpbb_quiz_nonce'])&&wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpbb_quiz_nonce'])),'wpbb_course_quiz_'.$course_id)){$score=0;foreach($questions as $q){$answer=sanitize_key(wp_unslash($_POST['answer'][$q->ID]??''));if($answer===get_post_meta($q->ID,'_quiz_correct',true))$score++;}}
 ob_start();?><section class="wpbb-course-quiz"><p class="wp-theme-sector-eyebrow"><?php echo esc_html(__( 'Knowledge check', 'wp-bbtheme-child-elearning' ));?></p><h2><?php echo esc_html(__( 'Check what you understood', 'wp-bbtheme-child-elearning' ));?></h2><?php if(null!==$score):?><div class="alert alert-success"><?php echo esc_html(sprintf(__( 'You scored %1$d out of %2$d.', 'wp-bbtheme-child-elearning' ),$score,count($questions)));?></div><?php endif;?><form method="post"><?php wp_nonce_field('wpbb_course_quiz_'.$course_id,'wpbb_quiz_nonce');?><input type="hidden" name="wpbb_quiz_course" value="<?php echo esc_attr($course_id);?>"><?php foreach($questions as $index=>$question):?><fieldset class="wpbb-sector-request"><legend><strong><?php echo esc_html(($index+1).'. '.get_post_meta($question->ID,'_quiz_question',true));?></strong></legend><?php foreach(array('a','b','c','d') as $letter):$label=get_post_meta($question->ID,'_quiz_choice_'.$letter,true);?><label style="display:flex;gap:10px;align-items:flex-start"><input type="radio" name="answer[<?php echo esc_attr($question->ID);?>]" value="<?php echo esc_attr($letter);?>" required><span><?php echo esc_html($label);?></span></label><?php endforeach;?></fieldset><?php endforeach;?><button class="btn btn-primary" type="submit"><?php echo esc_html(__( 'Submit quiz', 'wp-bbtheme-child-elearning' ));?></button></form></section><?php return ob_get_clean();
}
function wpbb_courses_learning_content($content){if(!is_singular('course')||!in_the_loop()||!is_main_query())return$content;return $content.wpbb_courses_curriculum_markup(get_the_ID()).wpbb_courses_quiz_markup(get_the_ID());} add_filter('the_content','wpbb_courses_learning_content',20);
function wpbb_courses_lesson_content($content){if(!is_singular('course_lesson')||!in_the_loop()||!is_main_query())return$content;$video=get_post_meta(get_the_ID(),'_course_video_url',true);$material=get_post_meta(get_the_ID(),'_course_material_url',true);$embed=$video?wp_oembed_get($video,array('width'=>960)):'';return '<section class="wpbb-sector-single"><div class="container"><p class="wp-theme-sector-eyebrow">'.esc_html(__( 'Course lesson', 'wp-bbtheme-child-elearning' )).'</p><h1>'.esc_html(get_the_title()).'</h1>'.($embed?'<div class="wpbb-course-video">'.$embed.'</div>':'').'<div class="wpbb-course-lesson-content">'.$content.'</div>'.($material?'<p><a class="btn btn-primary" href="'.esc_url($material).'" target="_blank" rel="noopener">'.esc_html(__( 'Open PDF material', 'wp-bbtheme-child-elearning' )).'</a></p>':'').'</div></section>';} add_filter('the_content','wpbb_courses_lesson_content',25);
function wpbb_courses_pll_learning($types,$settings){$types['course_lesson']='course_lesson';return$types;}add_filter('pll_get_post_types','wpbb_courses_pll_learning',10,2);

/**
 * v3.8.10.20: keep editable Mega Menu content out of public discovery / SEO.
 * The parent already registers these objects as private; child filters make the
 * intent explicit for Core XML sitemaps and common SEO plugins too.
 */
function wpbb_child_private_megamenu_post_type_args( $args, $post_type ) {
    if ( 'megamenu' !== $post_type ) return $args;
    $args['public'] = false;
    $args['publicly_queryable'] = false;
    $args['exclude_from_search'] = true;
    $args['has_archive'] = false;
    $args['rewrite'] = false;
    $args['query_var'] = false;
    return $args;
}
add_filter( 'register_post_type_args', 'wpbb_child_private_megamenu_post_type_args', 20, 2 );

function wpbb_child_private_megamenu_taxonomy_args( $args, $taxonomy ) {
    if ( 'megamenu-cat' !== $taxonomy ) return $args;
    $args['public'] = false;
    $args['publicly_queryable'] = false;
    $args['rewrite'] = false;
    $args['query_var'] = false;
    return $args;
}
add_filter( 'register_taxonomy_args', 'wpbb_child_private_megamenu_taxonomy_args', 20, 2 );

function wpbb_child_core_sitemap_post_types( $post_types ) {
    unset( $post_types['megamenu'] );
    return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'wpbb_child_core_sitemap_post_types', 20 );

function wpbb_child_core_sitemap_taxonomies( $taxonomies ) {
    unset( $taxonomies['megamenu-cat'] );
    return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'wpbb_child_core_sitemap_taxonomies', 20 );

function wpbb_child_mega_robots( $robots ) {
    if ( is_singular( 'megamenu' ) || is_tax( 'megamenu-cat' ) ) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
    }
    return $robots;
}
add_filter( 'wp_robots', 'wpbb_child_mega_robots', 20 );

function wpbb_child_yoast_exclude_megamenu_post_type( $excluded, $post_type ) {
    return 'megamenu' === $post_type ? true : $excluded;
}
add_filter( 'wpseo_sitemap_exclude_post_type', 'wpbb_child_yoast_exclude_megamenu_post_type', 20, 2 );

function wpbb_child_yoast_exclude_megamenu_taxonomy( $excluded, $taxonomy ) {
    return 'megamenu-cat' === $taxonomy ? true : $excluded;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'wpbb_child_yoast_exclude_megamenu_taxonomy', 20, 2 );

function wpbb_child_yoast_mega_robots( $robots ) {
    if ( is_singular( 'megamenu' ) || is_tax( 'megamenu-cat' ) ) return 'noindex, nofollow';
    return $robots;
}
add_filter( 'wpseo_robots', 'wpbb_child_yoast_mega_robots', 20 );


/**
 * v3.8.10.21: global request-a-quote UI is opt-in by child theme.
 * Sector themes with their own quote journeys can keep it; the rest do not
 * expose an unrelated floating "My Quote" control or public route.
 */
if ( ! function_exists( 'wpbb_child_request_quote_enabled' ) ) {
    function wpbb_child_request_quote_enabled() {
        $enabled_themes = array(
            'wp-bbtheme-child-automotive',
            'wp-bbtheme-child-building-services',
            'wp-bbtheme-child-insurance',
            'wp-bbtheme-child-logistics',
            'wp-bbtheme-child-medicine',
            'wp-bbtheme-child-woo-tech-shop',
        );
        $enabled = in_array( get_stylesheet(), $enabled_themes, true );
        return (bool) apply_filters( 'wpbb_child_request_quote_enabled', $enabled, get_stylesheet() );
    }
}

function wpbb_child_request_quote_body_class( $classes ) {
    $classes[] = wpbb_child_request_quote_enabled() ? 'wpbb-request-quote-enabled' : 'wpbb-request-quote-disabled';
    return $classes;
}
add_filter( 'body_class', 'wpbb_child_request_quote_body_class', 30 );

function wpbb_child_request_quote_menu_items( $items ) {
    if ( wpbb_child_request_quote_enabled() ) return $items;
    $target = trim( (string) wp_parse_url( home_url( '/request-a-quote/' ), PHP_URL_PATH ), '/' );
    foreach ( $items as $key => $item ) {
        $path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
        if ( $target && $path === $target ) unset( $items[ $key ] );
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'wpbb_child_request_quote_menu_items', 30 );

function wpbb_child_request_quote_disable_route() {
    if ( wpbb_child_request_quote_enabled() ) return;
    $request = isset( $GLOBALS['wp']->request ) ? trim( (string) $GLOBALS['wp']->request, '/' ) : '';
    if ( ! is_page( 'request-a-quote' ) && 'request-a-quote' !== $request ) return;

    global $wp_query;
    if ( $wp_query ) $wp_query->set_404();
    status_header( 404 );
    nocache_headers();
    $template = get_404_template();
    if ( $template ) {
        include $template;
        exit;
    }
    wp_die( esc_html__( 'Page not found.', 'wp-bbtheme-child' ), esc_html__( 'Not found', 'wp-bbtheme-child' ), array( 'response' => 404 ) );
}
add_action( 'template_redirect', 'wpbb_child_request_quote_disable_route', 1 );

function wpbb_child_request_quote_sitemap_args( $args, $post_type ) {
    if ( wpbb_child_request_quote_enabled() || 'page' !== $post_type ) return $args;
    $page = get_page_by_path( 'request-a-quote' );
    if ( $page ) {
        $excluded = isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array();
        $excluded[] = (int) $page->ID;
        $args['post__not_in'] = array_values( array_unique( $excluded ) );
    }
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'wpbb_child_request_quote_sitemap_args', 30, 2 );

require_once get_stylesheet_directory() . '/inc/seo-guardrails.php';

/** v3.8.10.24: identify generated legal pages independently of translated slugs. */
function wpbb_child_legal_page_body_class_v381024( $classes ) {
    if ( ! is_page() ) return $classes;
    $post = get_queried_object();
    if ( ! $post instanceof WP_Post ) return $classes;

    $is_legal = function_exists( 'is_privacy_policy' ) && is_privacy_policy();
    if ( ! $is_legal && false !== strpos( (string) $post->post_content, 'wp-theme-legal-section' ) ) {
        $is_legal = true;
    }
    if ( $is_legal ) $classes[] = 'wpbb-legal-page';
    return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'wpbb_child_legal_page_body_class_v381024', 40 );

/** v3.8.10.25: remove generated empty spacing without touching authored copy. */
if ( ! function_exists( 'wpbb_child_remove_empty_paragraphs_v381025' ) ) {
    function wpbb_child_remove_empty_paragraphs_v381025( $content ) {
        if ( is_admin() || ! is_string( $content ) || '' === $content ) return $content;
        return (string) preg_replace(
            '~<p(?:\\s[^>]*)?>(?:\\s|&nbsp;|&#160;|<br\\s*/?>)*</p>~i',
            '',
            $content
        );
    }
}
add_filter( 'the_content', 'wpbb_child_remove_empty_paragraphs_v381025', 120 );

/** v3.8.10.25: do not output a completely empty CTA block above the footer. */
if ( ! function_exists( 'wpbb_child_remove_empty_cta_v381025' ) ) {
    function wpbb_child_remove_empty_cta_v381025( $block_content, $block ) {
        if ( empty( $block['blockName'] ) || 'wpbb/cta-section' !== $block['blockName'] || ! is_string( $block_content ) ) return $block_content;
        if ( preg_match( '~<(?:img|picture|video|iframe|form|button|a)\\b~i', $block_content ) ) return $block_content;
        $plain = trim( html_entity_decode( wp_strip_all_tags( $block_content ), ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ) );
        return '' === $plain ? '' : $block_content;
    }
}
add_filter( 'render_block', 'wpbb_child_remove_empty_cta_v381025', 120, 2 );

