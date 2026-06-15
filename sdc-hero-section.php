<?php
/**
 * SDC Hero Section — WordPress Shortcode
 * 
 * Usage: [sdc_hero]
 * 
 * Paste this entire snippet into Code Snippets plugin (Functions type).
 * Then add [sdc_hero] in a Shortcode block on your homepage.
 * 
 * Supports French (default) and Arabic (/ar in URL) with full RTL.
 * The devis popup is in a separate file: sdc-devis-popup.php
 */

function sdc_hero_section_shortcode() {
    // Detect Arabic from URL
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_arabic = (strpos($request_uri, '/ar/') !== false || preg_match('#/ar$#', $request_uri));
    $dir = $is_arabic ? 'rtl' : 'ltr';
    $arrow = $is_arabic ? '←' : '→';

    // --- Text content per language ---
    if ($is_arabic) {
        $headline_line1 = 'نصنع تجارب رقمية';
        $headline_em1   = 'تُحرّك العلامات التجارية';
        $headline_line2 = 'وتُلهم';
        $headline_em2   = 'النموّ.';
        $btn_cta        = 'لدي مشروع أريد تنفيذه!';
        $btn_services   = 'خدماتنا';
    } else {
        $headline_line1 = 'Nous créons des expériences digitales';
        $headline_em1   = 'qui propulsent les marques';
        $headline_line2 = 'et inspirent';
        $headline_em2   = 'la croissance.';
        $btn_cta        = 'J’ai un projet à réaliser !';
        $btn_services   = 'Nos Services';
    }

    $services_url = $is_arabic ? '/ar/services' : '/services';

    ob_start();
    ?>
    <style>
    /* =========================================
       SDC Hero Section — Scoped Styles
       ========================================= */

    .sdc-hero-section *,
    .sdc-hero-section *::before,
    .sdc-hero-section *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    .sdc-hero-section {
      position: relative;
      width: 100vw;
      max-width: 100vw;
      margin-left: calc(-50vw + 50%);
      margin-right: calc(-50vw + 50%);
      min-height: 90vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #FAF9F6;
      overflow: hidden;
      padding: 80px 24px;
    }

    .sdc-hero-orb-container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 850px;
      max-width: 95%;
      height: auto;
      pointer-events: none;
      z-index: 1;
      animation: sdcFloatLogo 8s ease-in-out infinite;
    }

    @keyframes sdcFloatLogo {
      0% {
        transform: translate(-50%, -50%);
      }
      50% {
        transform: translate(-50%, -53%);
      }
      100% {
        transform: translate(-50%, -50%);
      }
    }

    .sdc-hero-orb {
      width: 100%;
      height: auto;
      opacity: 0.2;
      filter: blur(4px);
      transition: transform 0.35s cubic-bezier(0.25, 0.1, 0.25, 1);
      will-change: transform;
      display: block;
    }

    .sdc-hero-content {
      position: relative;
      z-index: 2;
      max-width: 820px;
      text-align: center;
      width: 100%;
    }

    .sdc-hero-headline {
      font-family: inherit;
      font-size: clamp(2rem, 5vw, 3.4rem);
      font-weight: 400;
      line-height: 1.3;
      color: #1a1a1a;
      margin-bottom: 40px;
      letter-spacing: -0.01em;
    }

    .sdc-hero-headline em {
      font-style: italic;
      font-weight: 400;
    }

    .sdc-hero-ctas {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 28px;
      flex-wrap: wrap;
    }

    .sdc-hero-btn-outline {
      display: inline-block;
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 500;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #ffffff;
      background: #F46036;
      border: 1.5px solid #F46036;
      border-radius: 50px;
      padding: 12px 28px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.3s ease, color 0.3s ease;
    }

    .sdc-hero-btn-outline:hover,
    .sdc-hero-btn-outline:focus {
      background: transparent;
      color: #F46036;
    }

    .sdc-hero-btn-text {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 500;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #F46036;
      background: none;
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: opacity 0.3s ease;
    }

    .sdc-hero-btn-text:hover,
    .sdc-hero-btn-text:focus {
      opacity: 0.7;
      text-decoration: underline;
      text-underline-offset: 4px;
    }

    .sdc-hero-btn-text .sdc-arrow {
      font-size: 1.1rem;
      transition: transform 0.3s ease;
    }

    .sdc-hero-btn-text:hover .sdc-arrow {
      transform: translateX(3px);
    }

    /* --- RTL adjustments --- */
    .sdc-hero-section[dir="rtl"] {
      direction: rtl;
    }

    .sdc-hero-section[dir="rtl"] .sdc-hero-headline {
      letter-spacing: 0;
    }

    .sdc-hero-section[dir="rtl"] .sdc-hero-btn-outline,
    .sdc-hero-section[dir="rtl"] .sdc-hero-btn-text {
      letter-spacing: 0;
    }

    .sdc-hero-section[dir="rtl"] .sdc-hero-btn-text:hover .sdc-arrow {
      transform: translateX(-3px);
    }

    @media (max-width: 768px) {
      .sdc-hero-section {
        min-height: 75vh;
        padding: 60px 20px;
      }

      .sdc-hero-orb-container {
        width: 500px;
        max-width: 140%;
      }

      .sdc-hero-orb {
        filter: blur(3px);
      }

      .sdc-hero-headline {
        font-size: clamp(1.6rem, 6vw, 2.2rem);
        margin-bottom: 32px;
      }

      .sdc-hero-btn-outline {
        padding: 10px 24px;
        font-size: 0.8rem;
      }

      .sdc-hero-btn-text {
        font-size: 0.8rem;
      }
    }
    </style>

    <section class="sdc-hero-section" dir="<?php echo $dir; ?>">
      <div class="sdc-hero-orb-container" aria-hidden="true">
        <img src="https://inersialab.com/wp-content/uploads/2026/06/inersialab_logo_icon_transparent.png" class="sdc-hero-orb" alt="" />
      </div>
      <div class="sdc-hero-content">
        <h1 class="sdc-hero-headline">
          <?php echo $headline_line1; ?>
          <em><?php echo $headline_em1; ?></em>
          <?php echo $headline_line2; ?>
          <em><?php echo $headline_em2; ?></em>
        </h1>
        <div class="sdc-hero-ctas">
          <a href="javascript:void(0);" class="sdc-hero-btn-outline" id="sdc-open-popup"><?php echo $btn_cta; ?></a>
          <a href="<?php echo $services_url; ?>" class="sdc-hero-btn-text">
            <?php echo $btn_services; ?> <span class="sdc-arrow"><?php echo $arrow; ?></span>
          </a>
        </div>
      </div>
    </section>

    <script>
    (function() {
      var section = document.querySelector('.sdc-hero-section');
      var orb = document.querySelector('.sdc-hero-orb');
      if (section && orb) {
        section.addEventListener('mousemove', function(e) {
          var rect = section.getBoundingClientRect();
          var xPercent = (e.clientX - rect.left) / rect.width - 0.5;
          var yPercent = (e.clientY - rect.top) / rect.height - 0.5;
          var moveX = xPercent * 80;
          var moveY = yPercent * 80;
          orb.style.transform = 'translate(' + moveX + 'px, ' + moveY + 'px)';
        });

        section.addEventListener('mouseleave', function() {
          orb.style.transform = 'translate(0, 0)';
        });
      }
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'sdc_hero', 'sdc_hero_section_shortcode' );
