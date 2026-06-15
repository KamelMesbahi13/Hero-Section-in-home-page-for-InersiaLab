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
    $arrow_card = $is_arabic ? '↖' : '↗';

    // --- Bilingual Content Assets ---
    if ($is_arabic) {
        $desc = 'حلول رقمية مبتكرة ومخصصة لدفع أعمالك إلى الأمام. من استراتيجية العلامة التجارية إلى التطوير التقني، نرافقك في كل خطوة.';
        $headline = 'تصميم منتجات رقمية فعالة وملهمة لنمو أعمالك';
        
        $card1_title = 'تصميم واجهة وتجربة المستخدم UI/UX';
        $card1_desc  = 'إنشاء واجهات حديثة ومبتكرة تركز على المستخدم وتضمن تجربة تصفح سلسة.';
        $card1_url   = '/ar/services#ui-ux';

        $card2_title = 'التطوير التقني والويب';
        $card2_desc  = 'تطوير تطبيقات ويب، تطبيقات جوال ومواقع تعريفية قوية، سريعة وقابلة للتطوير.';
        $card2_url   = '/ar/services#dev';

        $card3_title = 'الهوية البصرية والتسويق';
        $card3_desc  = 'تصميم العلامات التجارية، تحسين محركات البحث والتسويق الرقمي لمضاعفة أثرك.';
        $card3_url   = '/ar/services#branding';
    } else {
        $desc = 'Des solutions digitales sur mesure pour propulser votre activité. De la stratégie de marque au développement technique, nous vous accompagnons à chaque étape.';
        $headline = 'Concevoir des produits digitaux performants & inspirants pour votre croissance';

        $card1_title = 'Design UI / UX';
        $card1_desc  = 'Création d’interfaces modernes, intuitives et centrées utilisateur pour vos produits.';
        $card1_url   = '/services#ui-ux';

        $card2_title = 'Développement Technique';
        $card2_desc  = 'Applications web, mobile et sites vitrines performants, sécurisés et évolutifs.';
        $card2_url   = '/services#dev';

        $card3_title = 'Branding & Stratégie';
        $card3_desc  = 'Identité de marque forte, SEO et stratégies de marketing digital pour maximiser votre impact.';
        $card3_url   = '/services#branding';
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
      background-color: #FAF9F6; /* Cream/Ivory brand background */
      padding: 80px 24px;
      overflow: hidden;
    }

    .sdc-sh-container {
      max-width: 1140px;
      margin: 0 auto;
      width: 100%;
    }

    /* --- Top Layout Grid --- */
    .sdc-sh-top {
      display: grid;
      grid-template-columns: 35% 65%;
      gap: 48px;
      margin-bottom: 64px;
      align-items: start;
    }

    /* Description wrap with background dot pattern */
    .sdc-sh-desc-wrap {
      position: relative;
      padding-top: 36px;
      padding-left: 20px;
    }

    .sdc-sh-desc-wrap::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 120px;
      height: 150px;
      background-image: radial-gradient(rgba(244, 96, 54, 0.18) 1.5px, transparent 1.5px);
      background-size: 20px 20px;
      z-index: 1;
      pointer-events: none;
    }

    .sdc-sh-desc {
      position: relative;
      z-index: 2;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 0.95rem;
      line-height: 1.6;
      color: #666666;
    }

    /* Headline text styling */
    .sdc-sh-headline {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: clamp(1.8rem, 4.2vw, 3rem);
      font-weight: 700;
      line-height: 1.25;
      color: #0D1B2A; /* Dark navy color */
      letter-spacing: -0.015em;
    }

    /* --- Cards Row Grid --- */
    .sdc-sh-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-bottom: 64px;
    }

    .sdc-sh-card {
      border-top: 1.5px solid rgba(13, 27, 42, 0.12); /* Navy border line */
      padding-top: 24px;
      text-decoration: none;
      color: inherit;
      display: block;
      transition: border-color 0.4s ease;
    }

    .sdc-sh-card:hover {
      border-top-color: #F46036; /* Brand orange color */
    }

    .sdc-sh-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .sdc-sh-card-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #0D1B2A;
      transition: color 0.35s ease;
    }

    .sdc-sh-card:hover .sdc-sh-card-title {
      color: #F46036;
    }

    .sdc-sh-card-arrow {
      font-size: 1.15rem;
      font-weight: 600;
      color: rgba(13, 27, 42, 0.35);
      transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1), color 0.35s ease;
      line-height: 1;
    }

    .sdc-sh-card:hover .sdc-sh-card-arrow {
      transform: translate(4px, -4px); /* Moves top-right */
      color: #F46036;
    }

    .sdc-sh-card-desc {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 0.88rem;
      line-height: 1.5;
      color: #666666;
    }

    /* --- Bottom Featured Image --- */
    .sdc-sh-image-wrap {
      width: 100%;
      height: 480px;
      border-radius: 16px;
      overflow: hidden;
      position: relative;
      box-shadow: 0 20px 48px rgba(13, 27, 42, 0.08);
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
      padding-right: 20px;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap::before {
      left: auto;
      right: 0;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-card-title {
      letter-spacing: 0;
    }

    .sdc-services-hero[dir="rtl"] .sdc-sh-card:hover .sdc-sh-card-arrow {
      transform: translate(-4px, -4px); /* Moves top-left in RTL */
    }


    /* =========================================
       Responsive Media Queries
       ========================================= */

    @media (max-width: 992px) {
      .sdc-sh-top {
        gap: 36px;
      }
      .sdc-sh-cards {
        gap: 24px;
      }
      .sdc-sh-image-wrap {
        height: 380px;
      }
    }

    @media (max-width: 768px) {
      .sdc-services-hero {
        padding: 60px 20px;
      }

      .sdc-sh-top {
        grid-template-columns: 1fr !important;
        gap: 24px;
        margin-bottom: 40px;
      }

      .sdc-sh-desc-wrap {
        padding-left: 0;
        padding-right: 0;
        padding-top: 24px;
      }

      .sdc-services-hero[dir="rtl"] .sdc-sh-desc-wrap {
        padding-right: 0;
      }

      .sdc-sh-desc-wrap::before {
        width: 90px;
        height: 120px;
      }

      .sdc-sh-headline {
        font-size: clamp(1.6rem, 5.5vw, 2.2rem);
      }

      .sdc-sh-cards {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 48px;
      }

      .sdc-sh-card {
        padding-top: 18px;
      }

      .sdc-sh-image-wrap {
        height: 300px;
        border-radius: 12px;
      }
    }

    @media (max-width: 480px) {
      .sdc-sh-image-wrap {
        height: 220px;
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
          
          <a href="<?php echo esc_url($card1_url); ?>" class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card1_title); ?></span>
              <span class="sdc-sh-card-arrow"><?php echo esc_html($arrow_card); ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card1_desc); ?></p>
          </a>

          <a href="<?php echo esc_url($card2_url); ?>" class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card2_title); ?></span>
              <span class="sdc-sh-card-arrow"><?php echo esc_html($arrow_card); ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card2_desc); ?></p>
          </a>

          <a href="<?php echo esc_url($card3_url); ?>" class="sdc-sh-card">
            <div class="sdc-sh-card-header">
              <span class="sdc-sh-card-title"><?php echo esc_html($card3_title); ?></span>
              <span class="sdc-sh-card-arrow"><?php echo esc_html($arrow_card); ?></span>
            </div>
            <p class="sdc-sh-card-desc"><?php echo esc_html($card3_desc); ?></p>
          </a>

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
