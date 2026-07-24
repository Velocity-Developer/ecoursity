<?php

namespace Ecoursity\App;

use Ecoursity\App\Shortcodes\ButtonBuyCourseShortcode;
use Ecoursity\App\Shortcodes\CourseCardShortcode;
use Ecoursity\App\Shortcodes\CourseCurriculumShortcode;
use Ecoursity\App\Shortcodes\CourseDurationShortcode;
use Ecoursity\App\Shortcodes\CourseExcerptShortcode;
use Ecoursity\App\Shortcodes\CourseFaqShortcode;
use Ecoursity\App\Shortcodes\CourseHeroShortcode;
use Ecoursity\App\Shortcodes\CourseImageShortcode;
use Ecoursity\App\Shortcodes\CourseInstructorShortcode;
use Ecoursity\App\Shortcodes\CourseLabelShortcode;
use Ecoursity\App\Shortcodes\CourseLevelShortcode;
use Ecoursity\App\Shortcodes\CourseOverviewShortcode;
use Ecoursity\App\Shortcodes\CoursePriceShortcode;
use Ecoursity\App\Shortcodes\CourseSidebarShortcode;
use Ecoursity\App\Shortcodes\CourseTabsShortcode;
use Ecoursity\App\Shortcodes\CourseTitleShortcode;
use Ecoursity\App\Shortcodes\CourseUrlShortcode;

class Shortcode
{
    public function boot(): void
    {
        /**
         * [ecoursity-button-buy-course course_id="123" label="Buy Course" login_label="Login untuk Beli Course" free_label="Ambil Course Gratis" class="btn btn-primary"require_login="yes"]
         * **/
        add_shortcode('ecoursity-button-buy-course', [ButtonBuyCourseShortcode::class, 'render']);
        add_shortcode('ecoursity-course-card', [CourseCardShortcode::class, 'render']);
        add_shortcode('ecoursity-course-duration', [CourseDurationShortcode::class, 'render']);
        add_shortcode('ecoursity-course-excerpt', [CourseExcerptShortcode::class, 'render']);
        add_shortcode('ecoursity-course-hero', [CourseHeroShortcode::class, 'render']);
        add_shortcode('ecoursity-course-image', [CourseImageShortcode::class, 'render']);
        add_shortcode('ecoursity-course-label', [CourseLabelShortcode::class, 'render']);
        add_shortcode('ecoursity-course-level', [CourseLevelShortcode::class, 'render']);
        add_shortcode('ecoursity-course-price', [CoursePriceShortcode::class, 'render']);
        add_shortcode('ecoursity-course-tabs', [CourseTabsShortcode::class, 'render']);
        add_shortcode('ecoursity-course-title', [CourseTitleShortcode::class, 'render']);
        add_shortcode('ecoursity-course-url', [CourseUrlShortcode::class, 'render']);
        add_shortcode('ecoursity-course-overview', [CourseOverviewShortcode::class, 'render']);
        add_shortcode('ecoursity-course-curriculum', [CourseCurriculumShortcode::class, 'render']);
        add_shortcode('ecoursity-course-instructor', [CourseInstructorShortcode::class, 'render']);
        add_shortcode('ecoursity-course-faq', [CourseFaqShortcode::class, 'render']);
        add_shortcode('ecoursity-course-sidebar', [CourseSidebarShortcode::class, 'render']);
    }
}
