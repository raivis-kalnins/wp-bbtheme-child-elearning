<?php
/**
 * Native learning workflow for the E-Learning child theme.
 *
 * The module extends the existing course, lesson and quiz content model without
 * renaming post types or legacy metadata. It provides a visual curriculum
 * manager, course access settings, enrolment/progress tracking, richer quizzes,
 * student shortcodes and optional WooCommerce enrolment.
 */
defined('ABSPATH') || exit;

if (!function_exists('wpbb_lms_course_settings')) {
    function wpbb_lms_course_settings($course_id) {
        $access = sanitize_key((string) get_post_meta($course_id, '_wpbb_lms_access', true));
        if (!in_array($access, array('open', 'free', 'login', 'paid'), true)) $access = 'open';
        return array(
            'access'       => $access,
            'price'        => (string) (get_post_meta($course_id, '_wpbb_lms_price', true) ?: get_post_meta($course_id, '_courses_price', true)),
            'product_id'   => absint(get_post_meta($course_id, '_wpbb_lms_product_id', true)),
            'prerequisite' => absint(get_post_meta($course_id, '_wpbb_lms_prerequisite', true)),
            'drip_days'    => max(0, absint(get_post_meta($course_id, '_wpbb_lms_drip_days', true))),
            'pass_score'   => min(100, max(1, absint(get_post_meta($course_id, '_wpbb_lms_pass_score', true)) ?: 70)),
            'duration'     => (string) (get_post_meta($course_id, '_wpbb_lms_duration', true) ?: get_post_meta($course_id, '_courses_duration_hours', true)),
            'instructor'   => (string) (get_post_meta($course_id, '_wpbb_lms_instructor', true) ?: get_post_meta($course_id, '_courses_instructor', true)),
        );
    }
}

if (!function_exists('wpbb_lms_course_lessons')) {
    function wpbb_lms_course_lessons($course_id, $status = 'publish') {
        return get_posts(array(
            'post_type'      => 'course_lesson',
            'post_status'    => $status,
            'posts_per_page' => -1,
            'meta_key'       => '_course_course_id',
            'meta_value'     => absint($course_id),
            'orderby'        => array('menu_order' => 'ASC', 'ID' => 'ASC'),
            'order'          => 'ASC',
        ));
    }
}

if (!function_exists('wpbb_lms_course_quizzes')) {
    function wpbb_lms_course_quizzes($course_id, $status = 'publish') {
        return get_posts(array(
            'post_type'      => 'course_quiz',
            'post_status'    => $status,
            'posts_per_page' => -1,
            'meta_key'       => '_quiz_course_id',
            'meta_value'     => absint($course_id),
            'orderby'        => array('menu_order' => 'ASC', 'ID' => 'ASC'),
            'order'          => 'ASC',
        ));
    }
}

if (!function_exists('wpbb_lms_user_enrolments')) {
    function wpbb_lms_user_enrolments($user_id) {
        $value = get_user_meta(absint($user_id), '_wpbb_lms_enrolments', true);
        return is_array($value) ? $value : array();
    }
}

if (!function_exists('wpbb_lms_is_enrolled')) {
    function wpbb_lms_is_enrolled($user_id, $course_id) {
        $items = wpbb_lms_user_enrolments($user_id);
        return isset($items[absint($course_id)]);
    }
}

if (!function_exists('wpbb_lms_enrol_user')) {
    function wpbb_lms_enrol_user($user_id, $course_id, $source = 'manual') {
        $user_id = absint($user_id);
        $course_id = absint($course_id);
        if (!$user_id || !$course_id || 'course' !== get_post_type($course_id)) return false;
        $items = wpbb_lms_user_enrolments($user_id);
        if (!isset($items[$course_id])) {
            $items[$course_id] = array(
                'enrolled_at' => time(),
                'source'      => sanitize_key($source),
            );
            update_user_meta($user_id, '_wpbb_lms_enrolments', $items);
            do_action('wpbb_lms_user_enrolled', $user_id, $course_id, $source);
        }
        return true;
    }
}

if (!function_exists('wpbb_lms_progress_data')) {
    function wpbb_lms_progress_data($user_id) {
        $value = get_user_meta(absint($user_id), '_wpbb_lms_progress', true);
        return is_array($value) ? $value : array();
    }
}

if (!function_exists('wpbb_lms_completed_lessons')) {
    function wpbb_lms_completed_lessons($user_id, $course_id) {
        $progress = wpbb_lms_progress_data($user_id);
        $course = isset($progress[absint($course_id)]) && is_array($progress[absint($course_id)]) ? $progress[absint($course_id)] : array();
        $lessons = isset($course['lessons']) && is_array($course['lessons']) ? $course['lessons'] : array();
        return array_map('absint', array_keys($lessons));
    }
}

if (!function_exists('wpbb_lms_mark_lesson_complete')) {
    function wpbb_lms_mark_lesson_complete($user_id, $course_id, $lesson_id) {
        $user_id = absint($user_id);
        $course_id = absint($course_id);
        $lesson_id = absint($lesson_id);
        if (!$user_id || !$course_id || !$lesson_id) return false;
        if (absint(get_post_meta($lesson_id, '_course_course_id', true)) !== $course_id) return false;
        $progress = wpbb_lms_progress_data($user_id);
        if (!isset($progress[$course_id]) || !is_array($progress[$course_id])) $progress[$course_id] = array();
        if (!isset($progress[$course_id]['lessons']) || !is_array($progress[$course_id]['lessons'])) $progress[$course_id]['lessons'] = array();
        $progress[$course_id]['lessons'][$lesson_id] = time();
        $progress[$course_id]['updated_at'] = time();
        update_user_meta($user_id, '_wpbb_lms_progress', $progress);
        return true;
    }
}

if (!function_exists('wpbb_lms_record_quiz_score')) {
    function wpbb_lms_record_quiz_score($user_id, $course_id, $score, $total) {
        $user_id = absint($user_id);
        $course_id = absint($course_id);
        if (!$user_id || !$course_id) return;
        $progress = wpbb_lms_progress_data($user_id);
        if (!isset($progress[$course_id]) || !is_array($progress[$course_id])) $progress[$course_id] = array();
        if (!isset($progress[$course_id]['quizzes']) || !is_array($progress[$course_id]['quizzes'])) $progress[$course_id]['quizzes'] = array();
        $progress[$course_id]['quizzes'][] = array('score' => absint($score), 'total' => absint($total), 'date' => time());
        $progress[$course_id]['quizzes'] = array_slice($progress[$course_id]['quizzes'], -10);
        $progress[$course_id]['updated_at'] = time();
        update_user_meta($user_id, '_wpbb_lms_progress', $progress);
    }
}

if (!function_exists('wpbb_lms_progress_percent')) {
    function wpbb_lms_progress_percent($user_id, $course_id) {
        $lessons = wpbb_lms_course_lessons($course_id);
        if (!$lessons) return 0;
        $valid = wp_list_pluck($lessons, 'ID');
        $completed = array_intersect($valid, wpbb_lms_completed_lessons($user_id, $course_id));
        return min(100, (int) round((count($completed) / count($valid)) * 100));
    }
}

if (!function_exists('wpbb_lms_user_can_access_course')) {
    function wpbb_lms_user_can_access_course($course_id, $user_id = 0) {
        if (current_user_can('edit_post', absint($course_id))) return true;
        $settings = wpbb_lms_course_settings($course_id);
        if ('open' === $settings['access']) return true;
        $user_id = absint($user_id ?: get_current_user_id());
        if (!$user_id || !wpbb_lms_is_enrolled($user_id, $course_id)) return false;
        if ($settings['prerequisite'] && wpbb_lms_progress_percent($user_id, $settings['prerequisite']) < 100) return false;
        return true;
    }
}

if (!function_exists('wpbb_lms_course_access_markup')) {
    function wpbb_lms_course_access_markup($course_id) {
        $settings = wpbb_lms_course_settings($course_id);
        $user_id = get_current_user_id();
        if (wpbb_lms_user_can_access_course($course_id, $user_id)) {
            if (!$user_id) return '';
            $progress = wpbb_lms_progress_percent($user_id, $course_id);
            return '<aside class="wpbb-lms-access-card is-accessible"><div><p class="wp-theme-sector-eyebrow">' . esc_html__('Your progress', 'wp-bbtheme-child-elearning') . '</p><strong>' . esc_html(sprintf(__('%d%% complete', 'wp-bbtheme-child-elearning'), $progress)) . '</strong></div><div class="wpbb-lms-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr($progress) . '"><span style="width:' . esc_attr($progress) . '%"></span></div></aside>';
        }

        $title = __('Course access', 'wp-bbtheme-child-elearning');
        $text = __('Sign in or enrol to open the lessons, resources and knowledge checks.', 'wp-bbtheme-child-elearning');
        $action = '';
        if (!$user_id) {
            $action = '<a class="btn btn-primary" href="' . esc_url(wp_login_url(get_permalink($course_id))) . '">' . esc_html__('Sign in to continue', 'wp-bbtheme-child-elearning') . '</a>';
        } elseif ('paid' === $settings['access']) {
            if ($settings['product_id'] && function_exists('wc_get_product')) {
                $product = wc_get_product($settings['product_id']);
                if ($product) {
                    $action = '<a class="btn btn-primary" href="' . esc_url($product->add_to_cart_url()) . '">' . esc_html(sprintf(__('Buy course access%s', 'wp-bbtheme-child-elearning'), $product->get_price_html() ? ' — ' . wp_strip_all_tags($product->get_price_html()) : '')) . '</a>';
                }
            }
            if (!$action) $action = '<a class="btn btn-primary" href="' . esc_url(wp_theme_demo_page_url('contact')) . '">' . esc_html__('Ask about course access', 'wp-bbtheme-child-elearning') . '</a>';
        } else {
            $action = '<form method="post" class="wpbb-lms-enrol-form"><input type="hidden" name="wpbb_lms_action" value="enrol"><input type="hidden" name="course_id" value="' . esc_attr($course_id) . '">' . wp_nonce_field('wpbb_lms_enrol_' . $course_id, 'wpbb_lms_nonce', true, false) . '<button class="btn btn-primary" type="submit">' . esc_html__('Enrol in this course', 'wp-bbtheme-child-elearning') . '</button></form>';
        }
        return '<aside class="wpbb-lms-access-card is-locked"><div><p class="wp-theme-sector-eyebrow">' . esc_html($title) . '</p><h2>' . esc_html__('Unlock the curriculum', 'wp-bbtheme-child-elearning') . '</h2><p>' . esc_html($text) . '</p></div>' . $action . '</aside>';
    }
}

if (!function_exists('wpbb_lms_handle_enrolment')) {
    function wpbb_lms_handle_enrolment() {
        if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '') || 'enrol' !== sanitize_key((string) ($_POST['wpbb_lms_action'] ?? ''))) return;
        $course_id = absint($_POST['course_id'] ?? 0);
        if (!$course_id || 'course' !== get_post_type($course_id)) return;
        if (!is_user_logged_in()) auth_redirect();
        $nonce = sanitize_text_field(wp_unslash($_POST['wpbb_lms_nonce'] ?? ''));
        if (!wp_verify_nonce($nonce, 'wpbb_lms_enrol_' . $course_id)) wp_die(esc_html__('The enrolment request expired. Please try again.', 'wp-bbtheme-child-elearning'));
        $settings = wpbb_lms_course_settings($course_id);
        if ('paid' === $settings['access']) wp_die(esc_html__('This course requires a completed purchase.', 'wp-bbtheme-child-elearning'));
        wpbb_lms_enrol_user(get_current_user_id(), $course_id, 'self');
        wp_safe_redirect(add_query_arg('enrolled', '1', get_permalink($course_id)));
        exit;
    }
}
add_action('template_redirect', 'wpbb_lms_handle_enrolment', 4);

if (!function_exists('wpbb_lms_complete_lesson_action')) {
    function wpbb_lms_complete_lesson_action() {
        if (!is_user_logged_in()) auth_redirect();
        $lesson_id = absint($_POST['lesson_id'] ?? 0);
        $course_id = absint(get_post_meta($lesson_id, '_course_course_id', true));
        check_admin_referer('wpbb_lms_complete_' . $lesson_id);
        if (!wpbb_lms_user_can_access_course($course_id)) wp_die(esc_html__('You do not have access to this lesson.', 'wp-bbtheme-child-elearning'));
        wpbb_lms_mark_lesson_complete(get_current_user_id(), $course_id, $lesson_id);
        $next = wpbb_lms_adjacent_lesson($course_id, $lesson_id, 1);
        wp_safe_redirect($next ? get_permalink($next) : add_query_arg('course_complete', '1', get_permalink($course_id)));
        exit;
    }
}
add_action('admin_post_wpbb_lms_complete_lesson', 'wpbb_lms_complete_lesson_action');

if (!function_exists('wpbb_lms_adjacent_lesson')) {
    function wpbb_lms_adjacent_lesson($course_id, $lesson_id, $direction) {
        $ids = wp_list_pluck(wpbb_lms_course_lessons($course_id), 'ID');
        $index = array_search(absint($lesson_id), array_map('absint', $ids), true);
        if (false === $index) return 0;
        $target = $index + (int) $direction;
        return isset($ids[$target]) ? absint($ids[$target]) : 0;
    }
}

if (!function_exists('wpbb_lms_lesson_available')) {
    function wpbb_lms_lesson_available($lesson_id, $course_id, $user_id) {
        $preview = (bool) get_post_meta($lesson_id, '_wpbb_lms_preview', true);
        if ($preview || current_user_can('edit_post', $lesson_id)) return true;
        if (!wpbb_lms_user_can_access_course($course_id, $user_id)) return false;
        $drip = max(0, absint(get_post_meta($lesson_id, '_wpbb_lms_drip_days', true)));
        if (!$drip) return true;
        $enrolments = wpbb_lms_user_enrolments($user_id);
        $enrolled_at = absint($enrolments[$course_id]['enrolled_at'] ?? 0);
        return !$enrolled_at || time() >= ($enrolled_at + DAY_IN_SECONDS * $drip);
    }
}

if (!function_exists('wpbb_lms_curriculum_markup')) {
    function wpbb_lms_curriculum_markup($course_id) {
        $access = wpbb_lms_course_access_markup($course_id);
        $lessons = wpbb_lms_course_lessons($course_id);
        $user_id = get_current_user_id();
        if (!wpbb_lms_user_can_access_course($course_id, $user_id) && !$lessons) return $access;
        $completed = $user_id ? wpbb_lms_completed_lessons($user_id, $course_id) : array();
        $items = '';
        foreach ($lessons as $index => $lesson) {
            $duration = (string) get_post_meta($lesson->ID, '_course_duration', true);
            $available = wpbb_lms_lesson_available($lesson->ID, $course_id, $user_id);
            $is_complete = in_array(absint($lesson->ID), $completed, true);
            $preview = (bool) get_post_meta($lesson->ID, '_wpbb_lms_preview', true);
            $label = $is_complete ? __('Complete', 'wp-bbtheme-child-elearning') : ($preview ? __('Free preview', 'wp-bbtheme-child-elearning') : ($available ? __('Open lesson', 'wp-bbtheme-child-elearning') : __('Locked', 'wp-bbtheme-child-elearning')));
            $title = esc_html($lesson->post_title);
            $title_markup = $available ? '<a href="' . esc_url(get_permalink($lesson)) . '">' . $title . '</a>' : '<span>' . $title . '</span>';
            $items .= '<li class="wpbb-lms-curriculum-item' . ($is_complete ? ' is-complete' : '') . ($available ? '' : ' is-locked') . '"><span class="wpbb-lms-curriculum-item__number">' . esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) . '</span><div><h3>' . $title_markup . '</h3><p>' . esc_html($duration ?: __('Self-paced lesson', 'wp-bbtheme-child-elearning')) . '</p></div><span class="wpbb-lms-curriculum-item__status">' . esc_html($label) . '</span></li>';
        }
        if (!$items) $items = '<li class="wpbb-lms-curriculum-empty">' . esc_html__('Lessons will appear here after they are added in the Course Builder.', 'wp-bbtheme-child-elearning') . '</li>';
        return $access . '<section class="wpbb-course-curriculum wpbb-lms-curriculum"><div class="wpbb-lms-section-heading"><div><p class="wp-theme-sector-eyebrow">' . esc_html__('Curriculum', 'wp-bbtheme-child-elearning') . '</p><h2>' . esc_html__('A clear path through the course', 'wp-bbtheme-child-elearning') . '</h2></div><span>' . esc_html(sprintf(_n('%d lesson', '%d lessons', count($lessons), 'wp-bbtheme-child-elearning'), count($lessons))) . '</span></div><ol class="wpbb-lms-curriculum-list">' . $items . '</ol></section>';
    }
}

if (!function_exists('wpbb_lms_quiz_questions')) {
    function wpbb_lms_quiz_questions($quiz_id) {
        $json = (string) get_post_meta($quiz_id, '_wpbb_lms_questions_json', true);
        $decoded = json_decode($json, true);
        if (is_array($decoded) && $decoded) return $decoded;
        $question = trim((string) get_post_meta($quiz_id, '_quiz_question', true));
        if (!$question) return array();
        return array(array(
            'question'    => $question,
            'choices'     => array(
                'a' => (string) get_post_meta($quiz_id, '_quiz_choice_a', true),
                'b' => (string) get_post_meta($quiz_id, '_quiz_choice_b', true),
                'c' => (string) get_post_meta($quiz_id, '_quiz_choice_c', true),
                'd' => (string) get_post_meta($quiz_id, '_quiz_choice_d', true),
            ),
            'correct'     => sanitize_key((string) get_post_meta($quiz_id, '_quiz_correct', true)),
            'explanation' => (string) get_post_meta($quiz_id, '_quiz_explanation', true),
        ));
    }
}

if (!function_exists('wpbb_lms_quiz_markup')) {
    function wpbb_lms_quiz_markup($course_id) {
        if (!wpbb_lms_user_can_access_course($course_id)) return '';
        $quizzes = wpbb_lms_course_quizzes($course_id);
        $questions = array();
        foreach ($quizzes as $quiz) {
            foreach (wpbb_lms_quiz_questions($quiz->ID) as $index => $question) {
                if (!empty($question['question'])) {
                    $question['_key'] = $quiz->ID . '_' . $index;
                    $questions[] = $question;
                }
            }
        }
        if (!$questions) return '';

        $score = null;
        $feedback = array();
        if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && absint($_POST['wpbb_quiz_course'] ?? 0) === absint($course_id)) {
            $nonce = sanitize_text_field(wp_unslash($_POST['wpbb_quiz_nonce'] ?? ''));
            if (wp_verify_nonce($nonce, 'wpbb_course_quiz_' . $course_id)) {
                $score = 0;
                $answers = isset($_POST['wpbb_lms_answer']) && is_array($_POST['wpbb_lms_answer']) ? wp_unslash($_POST['wpbb_lms_answer']) : array();
                foreach ($questions as $index => $question) {
                    $answer = sanitize_key((string) ($answers[$question['_key']] ?? ''));
                    $correct = sanitize_key((string) ($question['correct'] ?? ''));
                    if ($answer && $answer === $correct) $score++;
                    $feedback[$index] = array('correct' => $answer === $correct, 'explanation' => (string) ($question['explanation'] ?? ''));
                }
                if (is_user_logged_in()) wpbb_lms_record_quiz_score(get_current_user_id(), $course_id, $score, count($questions));
            }
        }
        $settings = wpbb_lms_course_settings($course_id);
        $percent = null === $score ? 0 : (int) round(($score / count($questions)) * 100);
        ob_start(); ?>
        <section class="wpbb-course-quiz wpbb-lms-quiz">
            <p class="wp-theme-sector-eyebrow"><?php echo esc_html__('Knowledge check', 'wp-bbtheme-child-elearning'); ?></p>
            <h2><?php echo esc_html__('Check what you understood', 'wp-bbtheme-child-elearning'); ?></h2>
            <?php if (null !== $score) : ?>
                <div class="wpbb-lms-quiz-result <?php echo $percent >= $settings['pass_score'] ? 'is-pass' : 'is-review'; ?>">
                    <strong><?php echo esc_html(sprintf(__('%1$d of %2$d correct — %3$d%%', 'wp-bbtheme-child-elearning'), $score, count($questions), $percent)); ?></strong>
                    <span><?php echo esc_html($percent >= $settings['pass_score'] ? __('Pass achieved.', 'wp-bbtheme-child-elearning') : sprintf(__('Review the feedback and try again. Pass mark: %d%%.', 'wp-bbtheme-child-elearning'), $settings['pass_score'])); ?></span>
                </div>
            <?php endif; ?>
            <form method="post" class="wpbb-lms-quiz-form">
                <?php wp_nonce_field('wpbb_course_quiz_' . $course_id, 'wpbb_quiz_nonce'); ?>
                <input type="hidden" name="wpbb_quiz_course" value="<?php echo esc_attr($course_id); ?>">
                <?php foreach ($questions as $index => $question) : $choices = isset($question['choices']) && is_array($question['choices']) ? $question['choices'] : array(); ?>
                    <fieldset class="wpbb-lms-question">
                        <legend><span><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span><?php echo esc_html($question['question']); ?></legend>
                        <div class="wpbb-lms-question__choices">
                        <?php foreach ($choices as $letter => $choice) : if ('' === trim((string) $choice)) continue; ?>
                            <label><input type="radio" name="wpbb_lms_answer[<?php echo esc_attr($question['_key']); ?>]" value="<?php echo esc_attr($letter); ?>" required><span><?php echo esc_html($choice); ?></span></label>
                        <?php endforeach; ?>
                        </div>
                        <?php if (isset($feedback[$index])) : ?>
                            <p class="wpbb-lms-question__feedback <?php echo $feedback[$index]['correct'] ? 'is-correct' : 'is-incorrect'; ?>"><?php echo esc_html($feedback[$index]['correct'] ? __('Correct.', 'wp-bbtheme-child-elearning') : __('Not quite.', 'wp-bbtheme-child-elearning')); ?> <?php echo esc_html($feedback[$index]['explanation']); ?></p>
                        <?php endif; ?>
                    </fieldset>
                <?php endforeach; ?>
                <button class="btn btn-primary" type="submit"><?php echo esc_html__('Submit quiz', 'wp-bbtheme-child-elearning'); ?></button>
            </form>
        </section>
        <?php return ob_get_clean();
    }
}

if (!function_exists('wpbb_lms_lesson_content')) {
    function wpbb_lms_lesson_content($content) {
        $lesson_id = get_the_ID();
        $course_id = absint(get_post_meta($lesson_id, '_course_course_id', true));
        $available = $course_id && wpbb_lms_lesson_available($lesson_id, $course_id, get_current_user_id());
        if (!$available) {
            return '<section class="wpbb-sector-single wpbb-lms-lesson"><div class="container"><div class="wpbb-lms-access-card is-locked"><div><p class="wp-theme-sector-eyebrow">' . esc_html__('Lesson locked', 'wp-bbtheme-child-elearning') . '</p><h1>' . esc_html(get_the_title()) . '</h1><p>' . esc_html__('Enrol in the course or wait for this scheduled lesson to become available.', 'wp-bbtheme-child-elearning') . '</p></div><a class="btn btn-primary" href="' . esc_url($course_id ? get_permalink($course_id) : home_url('/courses/')) . '">' . esc_html__('Return to course', 'wp-bbtheme-child-elearning') . '</a></div></div></section>';
        }
        $video = (string) get_post_meta($lesson_id, '_course_video_url', true);
        $material = (string) get_post_meta($lesson_id, '_course_material_url', true);
        $embed = $video ? wp_oembed_get($video, array('width' => 1200)) : '';
        $previous = wpbb_lms_adjacent_lesson($course_id, $lesson_id, -1);
        $next = wpbb_lms_adjacent_lesson($course_id, $lesson_id, 1);
        $is_complete = is_user_logged_in() && in_array($lesson_id, wpbb_lms_completed_lessons(get_current_user_id(), $course_id), true);
        $progress = is_user_logged_in() ? wpbb_lms_progress_percent(get_current_user_id(), $course_id) : 0;
        ob_start(); ?>
        <section class="wpbb-sector-single wpbb-lms-lesson"><div class="container">
            <nav class="wpbb-lms-lesson-breadcrumb" aria-label="<?php esc_attr_e('Course navigation', 'wp-bbtheme-child-elearning'); ?>"><a href="<?php echo esc_url(get_permalink($course_id)); ?>"><?php echo esc_html(get_the_title($course_id)); ?></a><span aria-hidden="true">/</span><span><?php the_title(); ?></span></nav>
            <div class="wpbb-lms-lesson-layout">
                <main>
                    <p class="wp-theme-sector-eyebrow"><?php echo esc_html__('Course lesson', 'wp-bbtheme-child-elearning'); ?></p>
                    <h1><?php the_title(); ?></h1>
                    <?php if ($embed) : ?><div class="wpbb-course-video"><?php echo $embed; ?></div><?php endif; ?>
                    <div class="wpbb-course-lesson-content"><?php echo $content; ?></div>
                    <?php if ($material) : ?><p><a class="btn btn-outline-primary" href="<?php echo esc_url($material); ?>" target="_blank" rel="noopener"><?php echo esc_html__('Open lesson material', 'wp-bbtheme-child-elearning'); ?></a></p><?php endif; ?>
                </main>
                <aside class="wpbb-lms-lesson-sidebar">
                    <p class="wp-theme-sector-eyebrow"><?php echo esc_html__('Course progress', 'wp-bbtheme-child-elearning'); ?></p>
                    <strong><?php echo esc_html(sprintf(__('%d%% complete', 'wp-bbtheme-child-elearning'), $progress)); ?></strong>
                    <div class="wpbb-lms-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr($progress); ?>"><span style="width:<?php echo esc_attr($progress); ?>%"></span></div>
                    <?php if (is_user_logged_in() && !$is_complete) : ?><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="wpbb_lms_complete_lesson"><input type="hidden" name="lesson_id" value="<?php echo esc_attr($lesson_id); ?>"><?php wp_nonce_field('wpbb_lms_complete_' . $lesson_id); ?><button class="btn btn-primary" type="submit"><?php echo esc_html__('Mark complete', 'wp-bbtheme-child-elearning'); ?></button></form><?php elseif ($is_complete) : ?><p class="wpbb-lms-complete-label"><?php echo esc_html__('Lesson complete', 'wp-bbtheme-child-elearning'); ?></p><?php endif; ?>
                </aside>
            </div>
            <nav class="wpbb-lms-lesson-pager"><?php if ($previous) : ?><a href="<?php echo esc_url(get_permalink($previous)); ?>">← <?php echo esc_html(get_the_title($previous)); ?></a><?php else : ?><span></span><?php endif; ?><?php if ($next) : ?><a href="<?php echo esc_url(get_permalink($next)); ?>"><?php echo esc_html(get_the_title($next)); ?> →</a><?php else : ?><a href="<?php echo esc_url(get_permalink($course_id)); ?>"><?php echo esc_html__('Return to course', 'wp-bbtheme-child-elearning'); ?> →</a><?php endif; ?></nav>
        </div></section>
        <?php return ob_get_clean();
    }
}

/* Course builder and settings. */
if (!function_exists('wpbb_lms_register_meta_boxes')) {
    function wpbb_lms_register_meta_boxes() {
        add_meta_box('wpbb-lms-course-builder', __('Course Builder', 'wp-bbtheme-child-elearning'), 'wpbb_lms_course_builder_box', 'course', 'normal', 'high');
        add_meta_box('wpbb-lms-course-settings', __('Course Access', 'wp-bbtheme-child-elearning'), 'wpbb_lms_course_settings_box', 'course', 'side', 'high');
        add_meta_box('wpbb-lms-lesson-settings', __('Lesson Access', 'wp-bbtheme-child-elearning'), 'wpbb_lms_lesson_settings_box', 'course_lesson', 'side', 'default');
        add_meta_box('wpbb-lms-quiz-builder', __('Quiz Builder', 'wp-bbtheme-child-elearning'), 'wpbb_lms_quiz_builder_box', 'course_quiz', 'normal', 'high');
    }
}
add_action('add_meta_boxes', 'wpbb_lms_register_meta_boxes', 30);

if (!function_exists('wpbb_lms_course_builder_box')) {
    function wpbb_lms_course_builder_box($post) {
        wp_nonce_field('wpbb_lms_save_course_' . $post->ID, 'wpbb_lms_course_nonce');
        $items = array_merge(wpbb_lms_course_lessons($post->ID, array('publish', 'draft', 'pending', 'private')), wpbb_lms_course_quizzes($post->ID, array('publish', 'draft', 'pending', 'private')));
        usort($items, function($a, $b) { return ((int) $a->menu_order) <=> ((int) $b->menu_order); });
        echo '<div class="wpbb-lms-builder" data-course-id="' . esc_attr($post->ID) . '"><div class="wpbb-lms-builder__intro"><div><strong>' . esc_html__('Curriculum', 'wp-bbtheme-child-elearning') . '</strong><p>' . esc_html__('Drag lessons and quizzes into teaching order. Quick-add creates a draft, so existing saved content remains unchanged.', 'wp-bbtheme-child-elearning') . '</p></div><div class="wpbb-lms-builder__actions"><button type="button" class="button" data-wpbb-lms-add="course_lesson">' . esc_html__('Add lesson', 'wp-bbtheme-child-elearning') . '</button><button type="button" class="button" data-wpbb-lms-add="course_quiz">' . esc_html__('Add quiz', 'wp-bbtheme-child-elearning') . '</button></div></div><ol class="wpbb-lms-builder__list" data-wpbb-lms-list>';
        foreach ($items as $item) {
            $type = 'course_quiz' === $item->post_type ? __('Quiz', 'wp-bbtheme-child-elearning') : __('Lesson', 'wp-bbtheme-child-elearning');
            echo '<li draggable="true" data-id="' . esc_attr($item->ID) . '" data-type="' . esc_attr($item->post_type) . '"><span class="dashicons dashicons-menu" aria-hidden="true"></span><div><strong>' . esc_html($item->post_title) . '</strong><small>' . esc_html($type . ' · ' . ucfirst($item->post_status)) . '</small></div><a class="button-link" href="' . esc_url(get_edit_post_link($item->ID)) . '">' . esc_html__('Edit', 'wp-bbtheme-child-elearning') . '</a></li>';
        }
        echo '</ol><input type="hidden" name="wpbb_lms_curriculum_order" value="' . esc_attr(wp_json_encode(wp_list_pluck($items, 'ID'))) . '" data-wpbb-lms-order><p class="wpbb-lms-builder__empty"' . ($items ? ' hidden' : '') . '>' . esc_html__('Add the first lesson or quiz to begin the curriculum.', 'wp-bbtheme-child-elearning') . '</p></div>';
    }
}

if (!function_exists('wpbb_lms_course_settings_box')) {
    function wpbb_lms_course_settings_box($post) {
        $settings = wpbb_lms_course_settings($post->ID);
        $courses = get_posts(array('post_type' => 'course', 'post_status' => array('publish', 'draft'), 'posts_per_page' => -1, 'exclude' => array($post->ID), 'orderby' => 'title', 'order' => 'ASC'));
        ?>
        <p><label><strong><?php esc_html_e('Access mode', 'wp-bbtheme-child-elearning'); ?></strong><select class="widefat" name="wpbb_lms_course[access]"><?php foreach (array('open' => __('Open to everyone', 'wp-bbtheme-child-elearning'), 'free' => __('Free enrolment', 'wp-bbtheme-child-elearning'), 'login' => __('Signed-in learners', 'wp-bbtheme-child-elearning'), 'paid' => __('Paid via WooCommerce', 'wp-bbtheme-child-elearning')) as $value => $label) echo '<option value="' . esc_attr($value) . '" ' . selected($settings['access'], $value, false) . '>' . esc_html($label) . '</option>'; ?></select></label></p>
        <p><label><strong><?php esc_html_e('Linked product ID', 'wp-bbtheme-child-elearning'); ?></strong><input class="widefat" type="number" min="0" name="wpbb_lms_course[product_id]" value="<?php echo esc_attr($settings['product_id']); ?>"></label></p>
        <p><label><strong><?php esc_html_e('Display price', 'wp-bbtheme-child-elearning'); ?></strong><input class="widefat" name="wpbb_lms_course[price]" value="<?php echo esc_attr($settings['price']); ?>"></label></p>
        <p><label><strong><?php esc_html_e('Prerequisite course', 'wp-bbtheme-child-elearning'); ?></strong><select class="widefat" name="wpbb_lms_course[prerequisite]"><option value="0"><?php esc_html_e('None', 'wp-bbtheme-child-elearning'); ?></option><?php foreach ($courses as $course) echo '<option value="' . esc_attr($course->ID) . '" ' . selected($settings['prerequisite'], $course->ID, false) . '>' . esc_html($course->post_title) . '</option>'; ?></select></label></p>
        <p><label><strong><?php esc_html_e('Default drip delay (days)', 'wp-bbtheme-child-elearning'); ?></strong><input class="widefat" type="number" min="0" name="wpbb_lms_course[drip_days]" value="<?php echo esc_attr($settings['drip_days']); ?>"></label></p>
        <p><label><strong><?php esc_html_e('Quiz pass mark (%)', 'wp-bbtheme-child-elearning'); ?></strong><input class="widefat" type="number" min="1" max="100" name="wpbb_lms_course[pass_score]" value="<?php echo esc_attr($settings['pass_score']); ?>"></label></p>
        <?php
    }
}

if (!function_exists('wpbb_lms_lesson_settings_box')) {
    function wpbb_lms_lesson_settings_box($post) {
        $course_id = absint(get_post_meta($post->ID, '_course_course_id', true));
        $courses = get_posts(array('post_type' => 'course', 'post_status' => array('publish', 'draft'), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        wp_nonce_field('wpbb_lms_save_lesson_' . $post->ID, 'wpbb_lms_lesson_nonce');
        ?><p><label><strong><?php esc_html_e('Course', 'wp-bbtheme-child-elearning'); ?></strong><select class="widefat" name="wpbb_lms_lesson[course_id]"><option value="0"><?php esc_html_e('Select course', 'wp-bbtheme-child-elearning'); ?></option><?php foreach ($courses as $course) echo '<option value="' . esc_attr($course->ID) . '" ' . selected($course_id, $course->ID, false) . '>' . esc_html($course->post_title) . '</option>'; ?></select></label></p>
        <p><label><input type="checkbox" name="wpbb_lms_lesson[preview]" value="1" <?php checked((bool) get_post_meta($post->ID, '_wpbb_lms_preview', true)); ?>> <?php esc_html_e('Allow a public preview', 'wp-bbtheme-child-elearning'); ?></label></p>
        <p><label><strong><?php esc_html_e('Available after enrolment (days)', 'wp-bbtheme-child-elearning'); ?></strong><input class="widefat" type="number" min="0" name="wpbb_lms_lesson[drip_days]" value="<?php echo esc_attr(absint(get_post_meta($post->ID, '_wpbb_lms_drip_days', true))); ?>"></label></p><?php
    }
}

if (!function_exists('wpbb_lms_quiz_builder_box')) {
    function wpbb_lms_quiz_builder_box($post) {
        wp_nonce_field('wpbb_lms_save_quiz_' . $post->ID, 'wpbb_lms_quiz_nonce');
        $questions = wpbb_lms_quiz_questions($post->ID);
        if (!$questions) $questions = array(array('question' => '', 'choices' => array('a' => '', 'b' => '', 'c' => '', 'd' => ''), 'correct' => 'a', 'explanation' => ''));
        echo '<div class="wpbb-lms-question-builder" data-wpbb-lms-question-builder><div data-wpbb-lms-questions>';
        foreach ($questions as $index => $question) wpbb_lms_render_question_admin($index, $question);
        echo '</div><button type="button" class="button" data-wpbb-lms-add-question>' . esc_html__('Add question', 'wp-bbtheme-child-elearning') . '</button></div>';
    }
}

if (!function_exists('wpbb_lms_render_question_admin')) {
    function wpbb_lms_render_question_admin($index, $question) {
        $choices = isset($question['choices']) && is_array($question['choices']) ? $question['choices'] : array();
        echo '<section class="wpbb-lms-question-row" data-question-index="' . esc_attr($index) . '"><div class="wpbb-lms-question-row__head"><strong>' . esc_html(sprintf(__('Question %d', 'wp-bbtheme-child-elearning'), $index + 1)) . '</strong><button type="button" class="button-link-delete" data-wpbb-lms-remove-question>' . esc_html__('Remove', 'wp-bbtheme-child-elearning') . '</button></div><p><label>' . esc_html__('Question', 'wp-bbtheme-child-elearning') . '<textarea class="widefat" name="wpbb_lms_questions[' . esc_attr($index) . '][question]" rows="2">' . esc_textarea((string) ($question['question'] ?? '')) . '</textarea></label></p><div class="wpbb-lms-choice-grid">';
        foreach (array('a', 'b', 'c', 'd') as $letter) echo '<label><span>' . esc_html(strtoupper($letter)) . '</span><input class="widefat" name="wpbb_lms_questions[' . esc_attr($index) . '][choices][' . esc_attr($letter) . ']" value="' . esc_attr((string) ($choices[$letter] ?? '')) . '"></label>';
        echo '</div><div class="wpbb-lms-question-row__foot"><label>' . esc_html__('Correct answer', 'wp-bbtheme-child-elearning') . '<select name="wpbb_lms_questions[' . esc_attr($index) . '][correct]">';
        foreach (array('a', 'b', 'c', 'd') as $letter) echo '<option value="' . esc_attr($letter) . '" ' . selected((string) ($question['correct'] ?? 'a'), $letter, false) . '>' . esc_html(strtoupper($letter)) . '</option>';
        echo '</select></label><label>' . esc_html__('Explanation', 'wp-bbtheme-child-elearning') . '<input class="widefat" name="wpbb_lms_questions[' . esc_attr($index) . '][explanation]" value="' . esc_attr((string) ($question['explanation'] ?? '')) . '"></label></div></section>';
    }
}

if (!function_exists('wpbb_lms_save_course')) {
    function wpbb_lms_save_course($post_id, $post) {
        if (!$post || 'course' !== $post->post_type || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) return;
        $nonce = sanitize_text_field(wp_unslash($_POST['wpbb_lms_course_nonce'] ?? ''));
        if (!$nonce || !wp_verify_nonce($nonce, 'wpbb_lms_save_course_' . $post_id)) return;
        $input = isset($_POST['wpbb_lms_course']) && is_array($_POST['wpbb_lms_course']) ? wp_unslash($_POST['wpbb_lms_course']) : array();
        $access = sanitize_key((string) ($input['access'] ?? 'open'));
        if (!in_array($access, array('open', 'free', 'login', 'paid'), true)) $access = 'open';
        update_post_meta($post_id, '_wpbb_lms_access', $access);
        update_post_meta($post_id, '_wpbb_lms_product_id', absint($input['product_id'] ?? 0));
        update_post_meta($post_id, '_wpbb_lms_price', sanitize_text_field((string) ($input['price'] ?? '')));
        update_post_meta($post_id, '_wpbb_lms_prerequisite', absint($input['prerequisite'] ?? 0));
        update_post_meta($post_id, '_wpbb_lms_drip_days', max(0, absint($input['drip_days'] ?? 0)));
        update_post_meta($post_id, '_wpbb_lms_pass_score', min(100, max(1, absint($input['pass_score'] ?? 70))));
        if (isset($_POST['wpbb_lms_curriculum_order'])) {
            $ids = json_decode(wp_unslash((string) $_POST['wpbb_lms_curriculum_order']), true);
            if (is_array($ids)) foreach (array_values(array_unique(array_map('absint', $ids))) as $order => $item_id) {
                if (in_array(get_post_type($item_id), array('course_lesson', 'course_quiz'), true)) {
                    wp_update_post(array('ID' => $item_id, 'menu_order' => $order));
                }
            }
        }
    }
}
add_action('save_post_course', 'wpbb_lms_save_course', 40, 2);

if (!function_exists('wpbb_lms_save_lesson_settings')) {
    function wpbb_lms_save_lesson_settings($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) return;
        $nonce = sanitize_text_field(wp_unslash($_POST['wpbb_lms_lesson_nonce'] ?? ''));
        if (!$nonce || !wp_verify_nonce($nonce, 'wpbb_lms_save_lesson_' . $post_id)) return;
        $input = isset($_POST['wpbb_lms_lesson']) && is_array($_POST['wpbb_lms_lesson']) ? wp_unslash($_POST['wpbb_lms_lesson']) : array();
        update_post_meta($post_id, '_course_course_id', absint($input['course_id'] ?? 0));
        update_post_meta($post_id, '_wpbb_lms_preview', !empty($input['preview']) ? 1 : 0);
        update_post_meta($post_id, '_wpbb_lms_drip_days', max(0, absint($input['drip_days'] ?? 0)));
    }
}
add_action('save_post_course_lesson', 'wpbb_lms_save_lesson_settings', 40);

if (!function_exists('wpbb_lms_save_quiz_questions')) {
    function wpbb_lms_save_quiz_questions($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) return;
        $nonce = sanitize_text_field(wp_unslash($_POST['wpbb_lms_quiz_nonce'] ?? ''));
        if (!$nonce || !wp_verify_nonce($nonce, 'wpbb_lms_save_quiz_' . $post_id)) return;
        $rows = isset($_POST['wpbb_lms_questions']) && is_array($_POST['wpbb_lms_questions']) ? wp_unslash($_POST['wpbb_lms_questions']) : array();
        $clean = array();
        foreach ($rows as $row) {
            if (!is_array($row)) continue;
            $question = sanitize_textarea_field((string) ($row['question'] ?? ''));
            if ('' === $question) continue;
            $choices = array();
            foreach (array('a', 'b', 'c', 'd') as $letter) $choices[$letter] = sanitize_text_field((string) ($row['choices'][$letter] ?? ''));
            $correct = sanitize_key((string) ($row['correct'] ?? 'a'));
            if (!in_array($correct, array('a', 'b', 'c', 'd'), true)) $correct = 'a';
            $clean[] = array('question' => $question, 'choices' => $choices, 'correct' => $correct, 'explanation' => sanitize_textarea_field((string) ($row['explanation'] ?? '')));
        }
        update_post_meta($post_id, '_wpbb_lms_questions_json', wp_json_encode($clean));
    }
}
add_action('save_post_course_quiz', 'wpbb_lms_save_quiz_questions', 40);

if (!function_exists('wpbb_lms_ajax_quick_add')) {
    function wpbb_lms_ajax_quick_add() {
        if (!current_user_can('edit_posts')) wp_send_json_error(array('message' => __('Permission denied.', 'wp-bbtheme-child-elearning')), 403);
        check_ajax_referer('wpbb_lms_builder', 'nonce');
        $course_id = absint($_POST['course_id'] ?? 0);
        $type = sanitize_key((string) ($_POST['type'] ?? ''));
        $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
        if (!$course_id || 'course' !== get_post_type($course_id) || !in_array($type, array('course_lesson', 'course_quiz'), true)) wp_send_json_error(array('message' => __('Invalid course item.', 'wp-bbtheme-child-elearning')), 400);
        if (!$title) $title = 'course_quiz' === $type ? __('New quiz', 'wp-bbtheme-child-elearning') : __('New lesson', 'wp-bbtheme-child-elearning');
        $count = count(wpbb_lms_course_lessons($course_id, array('publish','draft','pending','private'))) + count(wpbb_lms_course_quizzes($course_id, array('publish','draft','pending','private')));
        $id = wp_insert_post(array('post_type' => $type, 'post_status' => 'draft', 'post_title' => $title, 'menu_order' => $count));
        if (!$id || is_wp_error($id)) wp_send_json_error(array('message' => __('The course item could not be created.', 'wp-bbtheme-child-elearning')), 500);
        update_post_meta($id, 'course_quiz' === $type ? '_quiz_course_id' : '_course_course_id', $course_id);
        wp_send_json_success(array('id' => $id, 'title' => $title, 'type' => $type, 'typeLabel' => 'course_quiz' === $type ? __('Quiz', 'wp-bbtheme-child-elearning') : __('Lesson', 'wp-bbtheme-child-elearning'), 'editUrl' => get_edit_post_link($id, 'raw')));
    }
}
add_action('wp_ajax_wpbb_lms_quick_add', 'wpbb_lms_ajax_quick_add');

if (!function_exists('wpbb_lms_admin_assets')) {
    function wpbb_lms_admin_assets($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) return;
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->post_type, array('course', 'course_lesson', 'course_quiz'), true)) return;
        $version = wp_get_theme()->get('Version');
        wp_enqueue_style('wpbb-lms-admin', get_stylesheet_directory_uri() . '/assets/admin/lms-admin.css', array('dashicons'), $version);
        wp_enqueue_script('wpbb-lms-admin', get_stylesheet_directory_uri() . '/assets/admin/lms-admin.js', array(), $version, true);
        wp_add_inline_script('wpbb-lms-admin', 'window.wpbbLmsAdmin=' . wp_json_encode(array('ajaxUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('wpbb_lms_builder'), 'promptLesson' => __('Lesson title', 'wp-bbtheme-child-elearning'), 'promptQuiz' => __('Quiz title', 'wp-bbtheme-child-elearning'), 'creating' => __('Creating…', 'wp-bbtheme-child-elearning'), 'failed' => __('The item could not be created.', 'wp-bbtheme-child-elearning'))) . ';', 'before');
    }
}
add_action('admin_enqueue_scripts', 'wpbb_lms_admin_assets');

if (!function_exists('wpbb_lms_course_catalog_shortcode')) {
    function wpbb_lms_course_catalog_shortcode($atts) {
        $atts = shortcode_atts(array('limit' => 9, 'category' => ''), $atts, 'wpbb_course_catalog');
        $args = array('post_type' => 'course', 'post_status' => 'publish', 'posts_per_page' => min(24, max(1, absint($atts['limit']))), 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'));
        if ($atts['category']) $args['tax_query'] = array(array('taxonomy' => 'course_category', 'field' => 'slug', 'terms' => sanitize_title($atts['category'])));
        $query = new WP_Query($args);
        if (!$query->have_posts()) return '<p>' . esc_html__('No courses found.', 'wp-bbtheme-child-elearning') . '</p>';
        ob_start(); echo '<div class="wpbb-lms-course-grid">';
        while ($query->have_posts()) { $query->the_post(); $settings = wpbb_lms_course_settings(get_the_ID()); echo '<article class="wpbb-lms-course-card"><a class="wpbb-lms-course-card__image" href="' . esc_url(get_permalink()) . '">' . (has_post_thumbnail() ? get_the_post_thumbnail(get_the_ID(), 'medium_large') : '') . '</a><div><p class="wp-theme-sector-eyebrow">' . esc_html($settings['access']) . '</p><h3><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3><p>' . esc_html(get_the_excerpt()) . '</p><a class="btn btn-outline-primary" href="' . esc_url(get_permalink()) . '">' . esc_html__('View course', 'wp-bbtheme-child-elearning') . '</a></div></article>'; }
        echo '</div>'; wp_reset_postdata(); return ob_get_clean();
    }
}
add_shortcode('wpbb_course_catalog', 'wpbb_lms_course_catalog_shortcode');

if (!function_exists('wpbb_lms_dashboard_shortcode')) {
    function wpbb_lms_dashboard_shortcode() {
        if (!is_user_logged_in()) return '<div class="wpbb-lms-access-card is-locked"><div><h2>' . esc_html__('Your learning dashboard', 'wp-bbtheme-child-elearning') . '</h2><p>' . esc_html__('Sign in to see enrolments and progress.', 'wp-bbtheme-child-elearning') . '</p></div><a class="btn btn-primary" href="' . esc_url(wp_login_url(get_permalink())) . '">' . esc_html__('Sign in', 'wp-bbtheme-child-elearning') . '</a></div>';
        $enrolments = wpbb_lms_user_enrolments(get_current_user_id());
        if (!$enrolments) return '<div class="wpbb-lms-dashboard-empty"><h2>' . esc_html__('Your learning dashboard', 'wp-bbtheme-child-elearning') . '</h2><p>' . esc_html__('You have not enrolled in a course yet.', 'wp-bbtheme-child-elearning') . '</p><a class="btn btn-primary" href="' . esc_url(get_post_type_archive_link('course')) . '">' . esc_html__('Browse courses', 'wp-bbtheme-child-elearning') . '</a></div>';
        ob_start(); echo '<section class="wpbb-lms-dashboard"><div class="wpbb-lms-section-heading"><div><p class="wp-theme-sector-eyebrow">' . esc_html__('Student area', 'wp-bbtheme-child-elearning') . '</p><h2>' . esc_html__('Your courses', 'wp-bbtheme-child-elearning') . '</h2></div></div><div class="wpbb-lms-dashboard-grid">';
        foreach ($enrolments as $course_id => $data) { if ('publish' !== get_post_status($course_id)) continue; $progress = wpbb_lms_progress_percent(get_current_user_id(), $course_id); echo '<article><div><h3><a href="' . esc_url(get_permalink($course_id)) . '">' . esc_html(get_the_title($course_id)) . '</a></h3><p>' . esc_html(sprintf(__('%d%% complete', 'wp-bbtheme-child-elearning'), $progress)) . '</p></div><div class="wpbb-lms-progress"><span style="width:' . esc_attr($progress) . '%"></span></div><a class="btn btn-outline-primary" href="' . esc_url(get_permalink($course_id)) . '">' . esc_html__('Continue learning', 'wp-bbtheme-child-elearning') . '</a></article>'; }
        echo '</div></section>'; return ob_get_clean();
    }
}
add_shortcode('wpbb_learning_dashboard', 'wpbb_lms_dashboard_shortcode');

if (!function_exists('wpbb_lms_woocommerce_enrolment')) {
    function wpbb_lms_woocommerce_enrolment($order_id) {
        if (!function_exists('wc_get_order')) return;
        $order = wc_get_order($order_id);
        if (!$order || !$order->get_user_id()) return;
        foreach ($order->get_items() as $item) {
            $product_id = absint($item->get_product_id());
            if (!$product_id) continue;
            $courses = get_posts(array('post_type' => 'course', 'post_status' => array('publish','private'), 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_wpbb_lms_product_id', 'meta_value' => $product_id));
            foreach ($courses as $course_id) wpbb_lms_enrol_user($order->get_user_id(), $course_id, 'woocommerce');
        }
    }
}
add_action('woocommerce_order_status_processing', 'wpbb_lms_woocommerce_enrolment');
add_action('woocommerce_order_status_completed', 'wpbb_lms_woocommerce_enrolment');

if (!function_exists('wpbb_lms_add_body_classes')) {
    function wpbb_lms_add_body_classes($classes) {
        if (is_singular('course')) $classes[] = 'wpbb-lms-course-page';
        if (is_singular('course_lesson')) $classes[] = 'wpbb-lms-lesson-page';
        return $classes;
    }
}
add_filter('body_class', 'wpbb_lms_add_body_classes');
