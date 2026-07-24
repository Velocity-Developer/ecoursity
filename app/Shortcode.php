<?php

namespace Ecoursity\App;

use Ecoursity\App\Shortcodes\ButtonBuyCourseShortcode;
use Ecoursity\App\Shortcodes\CourseCurriculumShortcode;
use Ecoursity\App\Shortcodes\CourseFaqShortcode;
use Ecoursity\App\Shortcodes\CourseHeroShortcode;
use Ecoursity\App\Shortcodes\CourseInstructorShortcode;
use Ecoursity\App\Shortcodes\CourseOverviewShortcode;
use Ecoursity\App\Shortcodes\CourseSidebarShortcode;
use Ecoursity\App\Shortcodes\CourseTabsShortcode;

class Shortcode
{
    public function boot(): void
    {
        /**
         * [ecoursity-button-buy-course course_id="123" label="Buy Course" login_label="Login untuk Beli Course" free_label="Ambil Course Gratis" class="btn btn-primary"require_login="yes"]
         * **/
        add_shortcode('ecoursity-button-buy-course', [ButtonBuyCourseShortcode::class, 'render']);
        add_shortcode('ecoursity-course-hero', [CourseHeroShortcode::class, 'render']);
        add_shortcode('ecoursity-course-tabs', [CourseTabsShortcode::class, 'render']);
        add_shortcode('ecoursity-course-overview', [CourseOverviewShortcode::class, 'render']);
        add_shortcode('ecoursity-course-curriculum', [CourseCurriculumShortcode::class, 'render']);
        add_shortcode('ecoursity-course-instructor', [CourseInstructorShortcode::class, 'render']);
        add_shortcode('ecoursity-course-faq', [CourseFaqShortcode::class, 'render']);
        add_shortcode('ecoursity-course-sidebar', [CourseSidebarShortcode::class, 'render']);
    }
}
