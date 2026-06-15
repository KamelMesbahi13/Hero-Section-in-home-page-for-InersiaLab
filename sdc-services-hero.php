<?php
/**
 * SDC Services Hero Section — WordPress Shortcode
 * 
 * Usage: [sdc_services_hero]
 * 
 * Paste this entire snippet into Code Snippets plugin (Functions type).
 * Then add [sdc_services_hero] in a Shortcode block on your services page.
 * 
 * Design Details:
 * - Clean modern web layout with asymmetrical 2-column description/headline.
 * - Dot grid background pattern under the description.
 * - Row of 3 card/links with top border hover animations.
 * - Full width bottom team workspace image with soft brand gradient filter.
 * - Supports French and Arabic (RTL) out of the box.
 */

function sdc_services_hero_section_shortcode() {
    // Detect Arabic from URL
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_arabic = (strpos($request_uri, '/ar/') !== false || preg_match('#/ar$#', $request_uri));
    $dir = $is_arabic ? 'rtl' : 'ltr';

    $icon_strategy = '
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sdc-sh-card-icon">
        <circle cx="12" cy="12" r="10"></circle>
        <circle cx="12" cy="12" r="2"></circle>
        <line x1="12" y1="2" x2="12" y2="6"></line>
        <line x1="12" y1="18" x2="12" y2="22"></line>
        <line x1="2" y1="12" x2="6" y2="12"></line>
        <line x1="18" y1="12" x2="22" y2="12"></line>
      </svg>';

    $icon_design = '
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sdc-sh-card-icon">
        <path d="M12 20h9"></path>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
      </svg>';

    $icon_development = '
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sdc-sh-card-icon">
        <polyline points="16 18 22 12 16 6"></polyline>
        <polyline points="8 6 2 12 8 18"></polyline>
      </svg>';

    // --- Bilingual Content Assets ---
    if ($is_arabic) {
        $desc = 'حلول رقمية مبتكرة ومخصصة لدفع أعمالك إلى الأمام. من استراتيجية العلامة التجارية إلى التطوير التقني، نرافقك في كل خطوة.';
        $headline = 'تصميم منتجات رقمية فعالة وملهمة لنمو أعمالك';
        
        $card1_title = '١. التخطيط والاستراتيجية';
        $card1_desc  = 'تحديد الأهداف، تحليل متطلباتكم، ورسم خريطة الطريق الواضحة لمشروعكم الرقمي.';
        $card1_url   = '/ar/services#strategy';

        $card2_title = '٢. التصميم وبناء النموذج';
        $card2_desc  = 'ابتكار واجهات جذابة، سهلة الاستخدام، ومصممة خصيصاً لتوفير تجربة مستخدم استثنائية.';
        $card2_url   = '/ar/services#design';

        $card3_title = '٣. التطوير والتشغيل';
        $card3_desc  = 'برمجة عالية الجودة، اختبارات دقيقة، وإطلاق حلول رقمية سريعة، آمنة وقابلة للتوسع.';
        $card3_url   = '/ar/services#launch';
    } else {
        $desc = 'Des solutions digitales sur mesure pour propulser votre activité. De la stratégie de marque au développement technique, nous vous accompagnons à chaque étape.';
        $headline = 'Concevoir des produits digitaux performants & inspirants pour votre croissance';

        $card1_title = '1. Stratégie & Cadrage';
        $card1_desc  = 'Définition des objectifs, analyse de vos besoins et planification de la feuille de route du produit.';
        $card1_url   = '/services#strategy';

        $card2_title = '2. Design & UX/UI';
        $card2_desc  = 'Création d’interfaces élégantes, modernes et optimisées pour assurer une navigation fluide.';
        $card2_url   = '/services#design';

        $card3_title = '3. Développement & Impact';
        $card3_desc  = 'Programmation robuste, tests rigoureux et déploiement d’un produit final performant.';
        $card3_url   = '/services#launch';
    }

    $img_url = 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80';

    ob_start();
    ?>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap');

    /* =========================================
       SDC Services Hero Section — Scoped Styles
       ========================================= */

    .sdc-services-hero *,
    .sdc-services-hero *::before,
    .sdc-services-hero *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    .sdc-services-hero {
      position: relative;
      width: 100vw;
      max-width: 100vw;
      margin-left: calc(-50vw + 50%);
      margin-right: calc(-50vw + 50%);
      padding: 15rem 2rem 5rem 2rem;
      overflow: hidden;
      font-family: 'Montserrat', sans-serif !important;
    }

    .sdc-sh-container {
      max-width: 71.25rem;
      margin: 0 auto;
      width: 100%;
    }

    /* --- Top Layout Grid --- */
    .sdc-sh-top {
      display: grid;
      grid-template-columns: 35% 65%;
      gap: 3rem;
      margin-bottom: 4rem;
      align-items: start;
    }

    /* Description wrap with background dot pattern */
    .sdc-sh-desc-wrap {
      position: relative;
      padding-top: 2.25rem;
      padding-left: 1.25rem;
    }

    .sdc-sh-desc-wrap::before {
      content: '';
      position: absolute;
      top: -2.5rem;
      left: -3.75rem;
      width: 13.75rem;
      height: 8.75rem;
      background-image: radial-gradient(rgba(244, 96, 54, 0.3) 0.156rem, transparent 0.156rem);
      background-size: 1.25rem 1.25rem;
      z-index: 1;
      pointer-events: none;
    }

    .sdc-sh-desc {
      position: relative;
      z-index: 2;
      font-family: 'Montserrat', sans-serif !important;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    /* Headline uses theme default h1 styles */

    /* --- Cards Row Grid --- */
    .sdc-sh-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2rem;
      margin-bottom: 4rem;
    }

    .sdc-sh-card {
      border-top: 0.094rem solid rgba(13, 27, 42, 0.12); /* Navy border line */
      padding-top: 1.5rem;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
      color: inherit !important;
      display: block;
      transition: border-color 0.4s ease;
    }
    .sdc-sh-card *,
    .sdc-sh-card:hover *,
    .sdc-sh-card:focus * {
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    .sdc-sh-card:hover {
      border-top-color: #F46036; /* Brand orange color */
    }

    .sdc-sh-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 0.75rem;
    }

    .sdc-sh-card-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      transition: color 0.35s ease;
    }

    .sdc-sh-card:hover .sdc-sh-card-title {
      color: #F46036;
    }

    .sdc-sh-card-icon-wrap {
      color: inherit;
      opacity: 0.35;
      transition: color 0.35s ease, opacity 0.35s ease, transform 0.35s ease;
      line-height: 1;
      display: inline-flex;
      align-items: center;
    }

    .sdc-sh-card:hover .sdc-sh-card-icon-wrap {
      color: #F46036 !important;
      opacity: 1 !important;
      transform: scale(1.1);
    }

    .sdc-sh-card-desc {
      font-family: 'Montserrat', sans-serif !important;
      font-size: 0.88rem;
      line-height: 1.5;
    }

    /* --- Bottom Featured Image --- */
    .sdc-sh-image-wrap {
      width: 100%;
      height: 30rem;
      border-radius: 1rem;
      overflow: hidden;
      position: relative;
      box-shadow: 0 1.25rem 3rem rgba(13, 27, 42, 0.08);
    }

    .sdc-sh-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      filter: grayscale(15%) contrast(105%);
      transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .sdc-sh-image-wrap::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      /* Gradient tint blending navy overlay with a soft orange accent gradient */
      background: linear-gradient(135deg, rgba(13, 27, 42, 0.22) 0%, rgba(244, 96, 54, 0.06) 100%);
      pointer-events: none;
    }

    .sdc-sh-image-wrap:hover .sdc-sh-image {
      transform: scale(1.025);
    }


    /* =========================================
       RTL Adjustments (mirrored alignments)
       ========================================= */

    .sdc-services-hero[dir="rtl"] {
      direction: rtl;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-top {
      grid-template-columns: 35% 65%;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap {
      padding-left: 0;
      padding-right: 1.25rem;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap::before {
      left: auto;
      right: -3.75rem;
      top: -2.5rem;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-card-title {
      letter-spacing: 0;
    }

    /* =========================================
       Responsive Media Queries
       ========================================= */

    @media (max-width: 62rem) {
      .sdc-sh-top {
        gap: 2.25rem;
      }
      .sdc-sh-cards {
        gap: 1.5rem;
      }
      .sdc-sh-image-wrap {
        height: 23.75rem;
      }
    }

    @media (max-width: 48rem) {
      .sdc-services-hero {
        padding: 9.375rem 2rem 3.75rem 2rem;
      }

      .sdc-sh-top {
        grid-template-columns: 1fr !important;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
      }

      .sdc-sh-headline {
        order: -1 !important;
      }

      .sdc-sh-desc-wrap {
        padding-left: 0;
        padding-right: 0;
        padding-top: 0 !important;
      }

      .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap {
        padding-right: 0;
      }

      .sdc-sh-desc-wrap::before {
        width: 11.25rem;
        height: 6.25rem;
        left: -1.25rem !important;
        top: -1.25rem !important;
      }
      .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap::before {
        left: auto !important;
        right: -1.25rem !important;
        top: -1.25rem !important;
      }

      /* Headline uses theme default h1 styles */

      .sdc-sh-cards {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 3rem;
      }

      .sdc-sh-card {
        padding-top: 1.125rem;
      }

      .sdc-sh-image-wrap {
        height: 18.75rem;
        border-radius: 0.75rem;
      }
    }

    @media (max-width: 30rem) {
      .sdc-sh-image-wrap {
        height: 13.75rem;
      }
    }
    </style>

    <section class="sdc-services-hero" dir="<?php echo $dir; ?>">
      <div class="sdc-sh-container">
        
        <!-- Top Section: Asymmetrical Grid -->
        <div class="sdc-sh-top">
          <div class="sdc-sh-desc-wrap">
            <p class="sdc-sh-desc"><?php echo esc_html($desc); ?></p>
          </div>
          <h1 class="sdc-sh-headline"><?php echo esc_html($headline); ?></h1>
        </div>

        <!-- Middle Section: Three Service Cards -->
        <div class="sdc-sh-cards">
          
          <div class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card1_title); ?></span>
              <span class="sdc-sh-card-icon-wrap"><?php echo $icon_strategy; ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card1_desc); ?></p>
          </div>

          <div class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card2_title); ?></span>
              <span class="sdc-sh-card-icon-wrap"><?php echo $icon_design; ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card2_desc); ?></p>
          </div>

          <div class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card3_title); ?></span>
              <span class="sdc-sh-card-icon-wrap"><?php echo $icon_development; ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card3_desc); ?></p>
          </div>

        </div>

        <!-- Bottom Section: Image with Overlay -->
        <div class="sdc-sh-image-wrap">
          <img src="<?php echo esc_url($img_url); ?>" class="sdc-sh-image" alt="InersiaLab Creative Team Working" />
        </div>

      </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'sdc_services_hero', 'sdc_services_hero_section_shortcode' );
