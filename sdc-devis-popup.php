<?php
/**
 * SDC Devis Popup — Multi-Step Bilingual Contact Form
 *
 * Triggered by the hero section button (id="sdc-open-popup") or any
 * element with class ".sdc-open-devis". Auto-renders in the footer.
 *
 * Features:
 * - 2-step animated form (Service Selection → Contact Info)
 * - Conditional sub-service dropdowns
 * - Bilingual FR/AR with full RTL support
 * - AJAX submission via wp_mail() to kmlmes13@gmail.com
 */

/* ─── AJAX Handler ──────────────────────────────────────────────── */
function sdc_devis_handle_submission() {
    if ( ! isset( $_POST['sdc_nonce'] ) || ! wp_verify_nonce( $_POST['sdc_nonce'], 'sdc_devis_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security verification failed.' ) );
    }

    $service     = sanitize_text_field( $_POST['service'] ?? '' );
    $sub_service = sanitize_text_field( $_POST['sub_service'] ?? '' );
    $fullname    = sanitize_text_field( $_POST['fullname'] ?? '' );
    $email       = sanitize_email( $_POST['email'] ?? '' );
    $phone       = sanitize_text_field( $_POST['phone'] ?? '' );
    $brief       = sanitize_textarea_field( $_POST['brief'] ?? '' );

    if ( empty( $service ) || empty( $fullname ) || empty( $email ) ) {
        wp_send_json_error( array( 'message' => 'Champs obligatoires manquants.' ) );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Email invalide.' ) );
    }

    $to      = 'kmlmes13@gmail.com';
    $subject = 'Nouvelle demande de devis — ' . $service;
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $fullname . ' <' . $email . '>',
    );

    $body = '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">'
        . '<div style="background:#1a1a1a;padding:24px 32px;border-radius:12px 12px 0 0;">'
        . '<h2 style="color:#FF8C61;margin:0;font-size:20px;">📋 Nouvelle Demande de Devis</h2>'
        . '</div>'
        . '<div style="background:#ffffff;padding:32px;border:1px solid #eee;border-top:none;border-radius:0 0 12px 12px;">'
        . '<table style="width:100%;border-collapse:collapse;">'
        . '<tr><td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#666;width:140px;">Service</td>'
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;">' . esc_html( $service ) . '</td></tr>'
        . '<tr><td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#666;">Sous-service</td>'
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;">' . esc_html( $sub_service ) . '</td></tr>'
        . '<tr><td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#666;">Nom complet</td>'
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;">' . esc_html( $fullname ) . '</td></tr>'
        . '<tr><td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#666;">Email</td>'
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;"><a href="mailto:' . esc_attr( $email ) . '" style="color:#FF8C61;">' . esc_html( $email ) . '</a></td></tr>'
        . '<tr><td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#666;">Téléphone</td>'
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;">' . esc_html( $phone ) . '</td></tr>'
        . '<tr><td style="padding:12px 0;color:#666;vertical-align:top;">Brief projet</td>'
        . '<td style="padding:12px 0;color:#1a1a1a;">' . nl2br( esc_html( $brief ) ) . '</td></tr>'
        . '</table></div></div>';

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( $sent ) {
        wp_send_json_success( array( 'message' => 'Email sent successfully.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to send email.' ) );
    }
}
add_action( 'wp_ajax_sdc_devis_submit', 'sdc_devis_handle_submission' );
add_action( 'wp_ajax_nopriv_sdc_devis_submit', 'sdc_devis_handle_submission' );


/* ─── Popup Render ──────────────────────────────────────────────── */
function sdc_devis_popup_render() {

    // ── Language Detection ──
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_arabic   = ( strpos( $request_uri, '/ar/' ) !== false || preg_match( '#/ar$#', $request_uri ) );
    $dir  = $is_arabic ? 'rtl' : 'ltr';
    $lang = $is_arabic ? 'ar' : 'fr';

    // ── Left Panel ──
    $panel_title_p1  = $is_arabic ? 'لديك' : 'Vous avez une';
    $panel_title_hl  = $is_arabic ? 'فكرة' : 'idée';
    $panel_title_p2  = $is_arabic ? 'مشروع في ذهنك؟<br class="sdc-br-desktop">لنبدأ العمل' : 'de projet en tête ?<br class="sdc-br-desktop">Commençons ensemble';
    $check1          = $is_arabic ? 'توقع رداً منا في غضون 24 ساعة' : 'Attendez-vous à une réponse sous 24h';
    $check2          = $is_arabic ? 'يمكننا توقيع اتفاقية سرية (NDA) قبل مناقشة مشروعك.' : 'Accord de confidentialité (NDA) possible avant discussion.';
    $check3          = $is_arabic ? 'الوصول إلى خبراء مخصصين لمشروعك' : 'Accès à des experts produit dédiés.';
    $book_call_label = $is_arabic ? 'احجز مكالمة مباشرة' : 'Book A Call Directly';
    $contact_label   = $is_arabic ? 'هل تفضل البريد الإلكتروني؟' : 'Preferred To Email?';

    // ── Step 1 ──
    $step1_title             = $is_arabic ? 'أي مشروع سنقوم بإطلاقه معاً؟' : 'Quel projet souhaitez-vous propulser ?';
    $service_label           = $is_arabic ? 'الخدمة'                : 'Service';
    $service_placeholder     = $is_arabic ? 'اختر خدمة...'          : 'Sélectionnez un service...';
    $sub_service_label       = $is_arabic ? 'الخدمة الفرعية'        : 'Sous-service';
    $sub_service_placeholder = $is_arabic ? 'اختر خدمة فرعية...'    : 'Sélectionnez un sous-service...';
    $btn_next                = $is_arabic ? 'التالي'                : 'Suivant';
    $arrow_next              = $is_arabic ? '←' : '→';

    // ── Step 2 ──
    $step2_title       = $is_arabic ? 'بيانات التواصل'             : 'Vos coordonnées';
    $name_label        = $is_arabic ? 'الاسم الكامل'               : 'Nom complet';
    $name_placeholder  = $is_arabic ? 'أدخل اسمك الكامل'           : 'Entrez votre nom complet';
    $email_label       = $is_arabic ? 'البريد الإلكتروني'          : 'Email';
    $email_placeholder = $is_arabic ? 'أدخل بريدك الإلكتروني'      : 'Entrez votre email';
    $phone_label       = $is_arabic ? 'رقم الهاتف'                 : 'Téléphone';
    $phone_placeholder = $is_arabic ? 'أدخل رقم هاتفك'             : 'Entrez votre numéro';
    $brief_label       = $is_arabic ? 'وصف المشروع'                : 'Brief du projet';
    $brief_placeholder = $is_arabic ? 'صف لنا مشروعك باختصار...'   : 'Décrivez votre projet en quelques mots...';
    $btn_back          = $is_arabic ? 'رجوع'                       : 'Retour';
    $btn_submit        = $is_arabic ? 'إرسال'                      : 'Envoyer';
    $arrow_back        = $is_arabic ? '→' : '←';

    // ── Feedback ──
    $err_required  = $is_arabic ? 'هذا الحقل مطلوب'                   : 'Ce champ est requis';
    $err_email     = $is_arabic ? 'البريد الإلكتروني غير صالح'        : 'Adresse email invalide';
    $err_generic   = $is_arabic ? 'حدث خطأ، حاول مرة أخرى.'          : 'Une erreur est survenue. Réessayez.';
    $success_title = $is_arabic ? 'شكراً لك !'                        : 'Merci !';
    $success_msg   = $is_arabic ? 'تم إرسال طلبك بنجاح. سنتواصل معك قريباً.' : 'Votre demande a été envoyée. Nous reviendrons vers vous sous 24h.';
    $close_label   = $is_arabic ? 'إغلاق' : 'Fermer';

    // ── Services Data ──
    $services = array(
        'fr' => array(
            array( 'label' => 'Développement Web',    'subs' => array( 'Site Vitrine', 'E-commerce', 'Application Web', 'Landing Page' ) ),
            array( 'label' => 'Design UI/UX',         'subs' => array( 'Maquette UI', 'Audit UX', 'Refonte Graphique', 'Design System' ) ),
            array( 'label' => 'Branding & Identité',  'subs' => array( 'Logo', 'Charte Graphique', 'Supports Print' ) ),
            array( 'label' => 'Marketing Digital',    'subs' => array( 'SEO', 'Google Ads', 'Réseaux Sociaux', 'Email Marketing' ) ),
            array( 'label' => 'Développement Mobile', 'subs' => array( 'Application iOS', 'Application Android', 'Application Hybride' ) ),
        ),
        'ar' => array(
            array( 'label' => 'تطوير الويب',       'subs' => array( 'موقع تعريفي', 'متجر إلكتروني', 'تطبيق ويب', 'صفحة هبوط' ) ),
            array( 'label' => 'تصميم UI/UX',       'subs' => array( 'تصميم واجهة', 'تدقيق تجربة المستخدم', 'إعادة تصميم', 'نظام تصميم' ) ),
            array( 'label' => 'العلامة التجارية',   'subs' => array( 'شعار', 'ميثاق بصري', 'مطبوعات' ) ),
            array( 'label' => 'التسويق الرقمي',    'subs' => array( 'تحسين محركات البحث', 'إعلانات جوجل', 'شبكات التواصل', 'التسويق بالبريد' ) ),
            array( 'label' => 'تطوير الجوال',      'subs' => array( 'تطبيق iOS', 'تطبيق أندرويد', 'تطبيق هجين' ) ),
        ),
    );

    $services_json = wp_json_encode( $services[ $lang ] );
    $ajax_url      = admin_url( 'admin-ajax.php' );
    $nonce         = wp_create_nonce( 'sdc_devis_nonce' );
    ?>

    <!-- ════════════════════════════════════════════════════════════
         SDC Devis Popup — Scoped Styles
         ════════════════════════════════════════════════════════════ -->
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap');

    /* ── Reset ── */
    .sdc-devis-overlay *,
    .sdc-devis-overlay *::before,
    .sdc-devis-overlay *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* ── Overlay ── */
    .sdc-devis-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease, visibility 0.35s ease;
        overscroll-behavior: contain !important;
    }
    .sdc-devis-overlay.sdc-active {
        visibility: visible;
        opacity: 1;
        pointer-events: all;
    }

    /* ── Popup Container ── */
    .sdc-devis-popup {
        display: flex;
        flex-direction: row;
        align-items: stretch;
        direction: ltr;
        max-width: 980px;
        width: 94%;
        height: auto;
        max-height: 92vh;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 32px 80px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0,0,0,0.04);
        transform: scale(0.94) translateY(20px);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    .sdc-devis-overlay.sdc-active .sdc-devis-popup {
        transform: scale(1) translateY(0);
    }

    /* ── Close Button ── */
    /* ── Close Button ── */
    .sdc-devis-overlay button.sdc-devis-close,
    .sdc-devis-overlay button#sdc-devis-close.sdc-devis-close {
        position: absolute !important;
        top: 20px !important;
        right: 20px !important;
        left: auto !important;
        z-index: 99999 !important;
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        max-width: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        background: rgba(0, 0, 0, 0.06) !important;
        background-color: rgba(0, 0, 0, 0.06) !important;
        border: none !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #666666 !important;
        cursor: pointer !important;
        box-shadow: none !important;
        text-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 1 !important;
        text-transform: none !important;
        transition: background-color 0.25s ease !important;
        outline: none !important;
    }
    
    .sdc-devis-overlay button.sdc-devis-close:hover,
    .sdc-devis-overlay button.sdc-devis-close:focus,
    .sdc-devis-overlay button.sdc-devis-close:active {
        background: rgba(0, 0, 0, 0.12) !important;
        background-color: rgba(0, 0, 0, 0.12) !important;
        color: #1a1a1a !important;
        box-shadow: none !important;
        border: none !important;
        outline: none !important;
    }

    .sdc-devis-overlay button.sdc-devis-close svg,
    .sdc-devis-overlay button#sdc-devis-close.sdc-devis-close svg {
        display: block !important;
        visibility: visible !important;
        width: 14px !important;
        height: 14px !important;
        min-width: 14px !important;
        min-height: 14px !important;
        margin: 0 !important;
        padding: 0 !important;
        opacity: 1 !important;
        overflow: visible !important;
    }

    .sdc-devis-overlay button.sdc-devis-close svg path,
    .sdc-devis-overlay button#sdc-devis-close.sdc-devis-close svg path {
        stroke: #666666 !important;
        stroke-width: 2.5px !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .sdc-devis-overlay button.sdc-devis-close:hover svg path {
        stroke: #1a1a1a !important;
    }

    /* ═══ LEFT PANEL ═══════════════════════════════════════════════ */
    .sdc-devis-left {
        flex: 0 0 50%; /* 50% width */
        background: #0D1B2A !important;
        background-image: radial-gradient(circle at 90% 80%, rgba(255, 140, 97, 0.08) 0%, transparent 65%) !important;
        padding: 48px 40px !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden !important; /* Clip the sliding/fading background image */
    }
    .sdc-devis-left-content-wrap {
        position: relative !important;
        z-index: 2 !important;
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        gap: 32px !important;
    }
    .sdc-devis-left-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 1.75rem !important; /* Increased text size to 1.75rem */
        font-weight: 800 !important;
        color: #ffffff !important; /* Force white color against theme */
        line-height: 1.35 !important;
        margin: 0 !important;
        letter-spacing: -0.02em !important;
        text-transform: none !important; /* Force natural case casing against theme capitalization */
    }
    .sdc-highlight {
        position: relative;
        display: inline-block;
    }
    .sdc-underline-curve {
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 8px;
        z-index: 1;
    }

    /* Checkmarks */
    .sdc-devis-checks {
        list-style: none;
        margin: 0 0 24px 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .sdc-devis-checks li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.88rem !important; /* Smaller text size */
        color: rgba(255, 255, 255, 0.85) !important; /* Force light white */
        line-height: 1.45 !important;
    }
    .sdc-check-svg {
        flex-shrink: 0;
        margin-top: 3px;
        color: #FF8C61 !important; /* Changed green to brand orange */
    }

    /* Book a Call Badge */
    .sdc-book-call-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        background: #111111 !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 50px !important;
        color: #ffffff !important;
        text-decoration: none !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.82rem !important; /* Smaller font */
        font-weight: 600 !important;
        margin-top: 8px;
        width: fit-content;
        transition: background 0.25s ease, border-color 0.25s ease;
    }
    .sdc-book-call-badge:hover {
        background: #222222 !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
    }
    .sdc-calendar-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #FF8C61 !important; /* Changed green to brand orange */
        color: #ffffff !important;
        flex-shrink: 0;
    }
    .sdc-badge-amp {
        color: rgba(255, 255, 255, 0.5) !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
    }
    .sdc-user-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #3f3f46 !important;
        color: #ffffff !important;
        font-size: 0.65rem !important;
        font-weight: 600 !important;
        flex-shrink: 0;
        text-transform: uppercase;
        font-family: sans-serif;
    }
    .sdc-badge-text {
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        color: #ffffff !important;
    }

    /* Contact info */
    .sdc-devis-left-contact {
        margin-top: auto;
    }
    .sdc-devis-contact-label {
        display: inline-block;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.8rem !important;
        color: rgba(255, 255, 255, 0.5) !important;
        margin-right: 6px;
    }
    .sdc-devis-contact-email {
        color: #ffffff !important;
        text-decoration: underline !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        transition: color 0.2s ease;
    }
    .sdc-devis-contact-email:hover {
        color: #FF8C61 !important;
    }
 
    /* ═══ RIGHT PANEL ═════════════════════════════════════════════ */
    .sdc-devis-right {
        flex: 1 1 50%; /* 50% width, can shrink/grow */
        background: #ffffff !important;
        background-image: radial-gradient(circle at 90% 85%, rgba(255, 140, 97, 0.07) 0%, transparent 65%) !important; /* Orange gradient glow in bottom-right */
        padding: 44px 40px;
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }
    .sdc-devis-right::-webkit-scrollbar { width: 4px; }
    .sdc-devis-right::-webkit-scrollbar-track { background: transparent; }
    .sdc-devis-right::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

    /* ── Step Dots ── */
    .sdc-devis-dots {
        display: flex;
        gap: 8px;
        margin-bottom: 28px;
    }
    .sdc-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e0e0e0;
        transition: background 0.35s ease, width 0.35s ease, border-radius 0.35s ease;
    }
    .sdc-dot.sdc-active {
        width: 28px;
        border-radius: 4px;
        background: #FF8C61;
    }

    /* ── Step Title ── */
    .sdc-devis-overlay h3.sdc-devis-step-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 1.45rem !important; /* Smaller, elegant header size */
        font-weight: 800 !important;
        color: #1a1a1a !important;
        margin: 0 0 24px 0 !important;
        line-height: 1.35 !important;
        letter-spacing: -0.015em !important;
        text-transform: none !important; /* Prevent theme Title Case override */
    }

    /* ── Steps ── */
    .sdc-devis-step { display: none; }
    .sdc-devis-step.sdc-active { display: block; }

    /* ── Form Fields ── */
    .sdc-devis-field {
        margin-bottom: 20px;
        position: relative;
    }
    .sdc-devis-label {
        display: block;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
        letter-spacing: 0.02em;
    }

    /* Chips selection */
    .sdc-services-grid,
    .sdc-sub-services-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 8px;
        margin-bottom: 8px;
    }
    
    .sdc-sub-service-group-title {
        font-family: inherit !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        color: #0d1b2a !important;
        opacity: 0.7 !important;
        margin: 14px 0 6px 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        width: 100% !important;
    }
    
    .sdc-sub-services-group-grid {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
    }
    
    .sdc-devis-overlay button.sdc-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px !important;
        font-family: inherit;
        font-size: 0.88rem !important;
        font-weight: 500 !important;
        color: #1a1a1a !important;
        background: #FAF9F6 !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 50px !important;
        cursor: pointer !important;
        transition: all 0.25s ease !important;
        user-select: none !important;
        box-shadow: none !important;
        height: auto !important;
        width: auto !important;
        min-height: auto !important;
        line-height: 1.2 !important;
        text-transform: none !important;
    }
    .sdc-devis-overlay button.sdc-chip:hover {
        border-color: #FF8C61 !important;
        color: #FF8C61 !important;
        background: rgba(255, 140, 97, 0.04) !important;
    }
    .sdc-devis-overlay button.sdc-chip.sdc-selected {
        background: #FF8C61 !important;
        border-color: #FF8C61 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(255, 140, 97, 0.2) !important;
    }

    /* Input */
    .sdc-devis-input {
        width: 100%;
        padding: 13px 18px;
        font-family: inherit;
        font-size: 0.92rem;
        color: #1a1a1a;
        background: #FAF9F6;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
    }
    .sdc-devis-input:focus {
        background: #ffffff;
        border-color: #FF8C61;
        box-shadow: 0 0 0 3px rgba(255, 140, 97, 0.08);
    }
    .sdc-devis-input::placeholder,
    .sdc-devis-textarea::placeholder {
        color: #bbb;
    }

    /* Textarea */
    .sdc-devis-textarea {
        width: 100%;
        padding: 13px 18px;
        font-family: inherit;
        font-size: 0.92rem;
        color: #1a1a1a;
        background: #FAF9F6;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        resize: vertical;
        min-height: 90px;
        transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
    }
    .sdc-devis-textarea:focus {
        background: #ffffff;
        border-color: #FF8C61;
        box-shadow: 0 0 0 3px rgba(255, 140, 97, 0.08);
    }

    /* Row (side-by-side fields) */
    .sdc-devis-row {
        display: flex;
        gap: 16px;
    }
    .sdc-devis-row .sdc-devis-field { flex: 1; }

    /* Sub-service reveal animation */
    .sdc-devis-field-sub {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        margin-bottom: 0;
        transition: max-height 0.6s ease, opacity 0.35s ease 0.05s, margin-bottom 0.3s ease;
    }
    .sdc-devis-field-sub.sdc-visible {
        max-height: 2000px; /* large enough for any number of selected categories */
        opacity: 1;
        margin-bottom: 20px;
    }

    /* ── Error State ── */
    .sdc-devis-field.sdc-error .sdc-devis-input,
    .sdc-devis-field.sdc-error .sdc-devis-textarea,
    .sdc-devis-field.sdc-error .sdc-chip {
        border-color: #e74c3c !important;
    }
    .sdc-devis-field.sdc-error .sdc-devis-input:focus,
    .sdc-devis-field.sdc-error .sdc-devis-textarea:focus {
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.08) !important;
    }
    .sdc-devis-error-msg {
        color: #e74c3c;
        font-family: inherit;
        font-size: 0.76rem;
        margin-top: 6px;
        display: none;
    }
    .sdc-devis-field.sdc-error .sdc-devis-error-msg {
        display: block;
        animation: sdcShake 0.3s ease;
    }

    /* ── Actions Row ── */
    .sdc-devis-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 32px;
    }

    /* Primary Button */
    .sdc-devis-overlay .sdc-devis-btn-primary {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
        min-width: 192px !important;
        width: auto !important;
        height: 56px !important;
        padding: 0 28px 0 60px !important;
        background-color: #ffffff !important;
        color: #060D14 !important;
        border: 1.5px solid rgba(30, 58, 95, 0.12) !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        cursor: pointer !important;
        text-decoration: none !important;
        font-family: 'Montserrat', sans-serif !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        transition: all 0.75s cubic-bezier(0.25, 1, 0.5, 1) !important;
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.05) !important;
        outline: none !important;
        z-index: 1 !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary:hover {
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(255, 140, 97, 0.15) !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary::before {
        content: '' !important;
        position: absolute !important;
        left: 4px !important;
        top: 4px !important;
        height: 48px !important; 
        width: 48px !important;  
        background-color: #FF8C61 !important;
        border-radius: 12px !important;
        z-index: 1 !important;
        transition: all 0.75s cubic-bezier(0.25, 1, 0.5, 1) !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary:hover::before {
        width: calc(100% - 8px) !important; 
    }

    .sdc-devis-overlay .sdc-devis-btn-primary::after {
        content: '' !important;
        position: absolute !important;
        left: 16px !important; 
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 24px !important;
        height: 24px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1024 1024'%3E%3Cpath d='M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z' fill='%23ffffff'/%3E%3Cpath d='m786.752 512-265.408-265.344a32 32 0 0 1 45.312-45.312l288 288a32 32 0 0 1 0 45.312l-288 288a32 32 0 1 1-45.312-45.312L786.752 512z' fill='%23ffffff'/%3E%3C/svg%3E") !important;
        background-size: contain !important;
        background-repeat: no-repeat !important;
        z-index: 2 !important;
        transition: all 0.75s cubic-bezier(0.25, 1, 0.5, 1) !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary:hover::after {
        left: 50% !important; 
        transform: translate(-50%, -50%) !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary .sdc-btn-text {
        position: relative !important;
        z-index: 3 !important;
        display: inline-block !important;
        transform: translateX(0) !important;
        opacity: 1 !important;
        transition: all 0.75s cubic-bezier(0.25, 1, 0.5, 1) !important;
    }

    .sdc-devis-overlay .sdc-devis-btn-primary:hover .sdc-btn-text {
        transform: translateX(26px) !important;
        opacity: 0 !important;
    }

    /* Loading state */
    .sdc-devis-btn-primary.sdc-loading {
        pointer-events: none !important;
    }
    .sdc-devis-btn-primary.sdc-loading .sdc-btn-text {
        opacity: 0 !important;
    }
    .sdc-devis-btn-primary.sdc-loading::after {
        display: none !important;
    }
    .sdc-devis-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(0,0,0,0.1);
        border-top-color: #FF8C61;
        border-radius: 50%;
        animation: sdcSpin 0.6s linear infinite;
        position: absolute !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 4 !important;
    }
    .sdc-devis-btn-primary.sdc-loading .sdc-devis-spinner {
        display: block !important;
    }

    /* Back Button specific override */
    .sdc-devis-overlay button.sdc-devis-btn-back {
        background: transparent !important;
        border: none !important;
        color: #999 !important;
        box-shadow: none !important;
        padding: 8px 4px !important;
        height: auto !important;
        width: auto !important;
        min-height: auto !important;
        line-height: inherit !important;
        text-transform: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-family: inherit !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        cursor: pointer !important;
        transition: color 0.25s ease !important;
    }
    .sdc-devis-overlay button.sdc-devis-btn-back:hover {
        color: #FF8C61 !important;
        background: transparent !important;
    }

    /* Button arrow */
    .sdc-btn-arrow {
        font-size: 1rem;
        line-height: 1;
        transition: transform 0.25s ease;
    }
    .sdc-devis-btn-primary:hover .sdc-btn-arrow {
        transform: translateX(3px);
    }
    .sdc-devis-overlay button.sdc-devis-btn-back:hover .sdc-btn-arrow {
        transform: translateX(-3px) !important;
    }

    /* Submit error */
    .sdc-devis-submit-error {
        color: #e74c3c;
        font-family: inherit;
        font-size: 0.8rem;
        text-align: center;
        margin-top: 16px;
    }

    /* ═══ SUCCESS STATE ═══════════════════════════════════════════ */
    .sdc-devis-success {
        display: none;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        text-align: center;
        padding: 40px;
        z-index: 5;
    }
    .sdc-devis-success.sdc-active {
        display: flex;
        animation: sdcFadeIn 0.4s ease;
    }
    .sdc-devis-success-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #FF8C61, #E57246);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        animation: sdcScaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s both;
        box-shadow: 0 8px 24px rgba(255, 140, 97, 0.3);
    }
    .sdc-devis-success-title {
        font-family: inherit;
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .sdc-devis-success-msg {
        font-family: inherit;
        font-size: 0.95rem;
        color: #666;
        line-height: 1.6;
        max-width: 320px;
    }

    /* ═══ ANIMATIONS ═════════════════════════════════════════════ */
    @keyframes sdcSlideInRight {
        from { opacity: 0; transform: translateX(28px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes sdcSlideInLeft {
        from { opacity: 0; transform: translateX(-28px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes sdcFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes sdcScaleIn {
        from { opacity: 0; transform: scale(0.5); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes sdcSpin {
        to { transform: rotate(360deg); }
    }
    @keyframes sdcShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }

    /* ═══ RTL OVERRIDES ══════════════════════════════════════════ */
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-close,
    .sdc-devis-overlay[dir="rtl"] button#sdc-devis-close.sdc-devis-close {
        right: auto !important;
        left: 20px !important;
    }
    @media (min-width: 769px) {
        .sdc-devis-overlay[dir="rtl"] .sdc-devis-popup {
            flex-direction: row-reverse !important;
        }
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-left,
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-right {
        direction: rtl;
        text-align: right;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-right {
        background-image: radial-gradient(circle at 10% 85%, rgba(255, 140, 97, 0.07) 0%, transparent 65%) !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary:hover .sdc-btn-arrow {
        transform: translateX(-3px);
    }
    .sdc-devis-overlay[dir="rtl"] button.sdc-devis-btn-back:hover .sdc-btn-arrow {
        transform: translateX(3px) !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-contact-label {
        margin-right: 0;
        margin-left: 6px;
    }
    
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-actions {
        flex-direction: row-reverse !important;
    }
    
    /* RTL overrides for the primary button */
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary {
        padding: 0 60px 0 28px !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary::before {
        left: auto !important;
        right: 4px !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary::after {
        left: auto !important;
        right: 16px !important;
        transform: translateY(-50%) scaleX(-1) !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary:hover::after {
        right: 50% !important;
        left: auto !important;
        transform: translate(50%, -50%) scaleX(-1) !important;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary:hover .sdc-btn-text {
        transform: translateX(-26px) !important;
    }

    /* ═══ RESPONSIVE ═════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .sdc-br-desktop {
            display: none !important;
        }
        .sdc-devis-popup {
            flex-direction: column !important;
            width: 96% !important;
            max-width: 540px !important;
            height: 85vh !important;
            max-height: 85vh !important;
            border-radius: 16px !important;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15) !important;
            display: flex !important;
            overflow: hidden !important;
        }
        /* Mobile left panel: ~30% of popup height, text centered */
        .sdc-devis-left {
            display: flex !important;
            flex: 0 0 30vh !important;
            height: 30vh !important;
            width: 100% !important;
            padding: 28px !important;
            min-height: auto !important;
            justify-content: center !important;
            align-items: center !important;
            flex-direction: column !important;
            text-align: left !important;
            transition: flex-basis 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                        height 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                        padding 0.4s ease !important;
        }
        /* RTL Mobile left panel alignment override */
        .sdc-devis-overlay[dir="rtl"] .sdc-devis-left {
            text-align: right !important;
        }
        /* Collapsed state: taller strip with breathing room */
        .sdc-devis-left.sdc-left-collapsed {
            flex: 0 0 120px !important;
            height: 120px !important;
            padding: 20px 28px !important;
            justify-content: center !important;
            align-items: flex-start !important;
        }
        .sdc-devis-left-content-wrap {
            gap: 0 !important;
            justify-content: center !important;
            height: auto !important;
            width: 100% !important;
        }
        /* Hide checklist and contact on mobile */
        .sdc-devis-left .sdc-devis-left-middle,
        .sdc-devis-left .sdc-devis-left-contact {
            display: none !important;
        }
        /* Title sizing */
        .sdc-devis-left-title {
            font-size: 1.25rem !important;
            line-height: 1.35 !important;
            margin: 0 !important;
            transition: font-size 0.4s ease !important;
        }
        .sdc-devis-left.sdc-left-collapsed .sdc-devis-left-title {
            font-size: 1rem !important;
            line-height: 1.3 !important;
        }
        .sdc-devis-right {
            flex: 1 1 0 !important;
            width: 100% !important;
            min-height: 0 !important;
            padding: 28px 24px 24px 24px !important;
            background-color: #ffffff !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            overscroll-behavior: contain !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .sdc-devis-field {
            margin-bottom: 22px !important;
        }
        .sdc-devis-label {
            margin-bottom: 8px !important;
        }
        .sdc-devis-actions {
            margin-top: 30px !important;
        }
        .sdc-devis-overlay button.sdc-devis-close {
            background: rgba(0, 0, 0, 0.06) !important;
            background-color: rgba(0, 0, 0, 0.06) !important;
            color: #666666 !important;
            box-shadow: none !important;
            top: 16px !important;
            right: 16px !important;
        }
        .sdc-devis-overlay button.sdc-devis-close svg path {
            stroke: #666666 !important;
        }
        .sdc-devis-overlay button.sdc-devis-close:hover {
            background: rgba(0, 0, 0, 0.12) !important;
            background-color: rgba(0, 0, 0, 0.12) !important;
            color: #1a1a1a !important;
        }
        .sdc-devis-overlay button.sdc-devis-close:hover svg path {
            stroke: #1a1a1a !important;
        }
        .sdc-devis-overlay[dir="rtl"] button.sdc-devis-close {
            right: auto !important;
            left: 16px !important;
        }
        .sdc-devis-row {
            flex-direction: column !important;
            gap: 0 !important;
        }
        .sdc-devis-overlay h3.sdc-devis-step-title { font-size: 1.25rem !important; }
    }

    @media (max-width: 480px) {
        .sdc-devis-popup {
            width: 96% !important;
            height: 85vh !important;
            max-height: 85vh !important;
        }
        .sdc-devis-left {
            flex: 0 0 28vh !important;
            height: 28vh !important;
            padding: 24px 20px !important;
        }
        .sdc-devis-left.sdc-left-collapsed {
            flex: 0 0 150px !important;
            height: 150px !important;
            padding: 22px 20px !important;
        }
        .sdc-devis-left-title {
            font-size: 1.25rem !important;
        }
        .sdc-devis-left.sdc-left-collapsed .sdc-devis-left-title {
            font-size: 1.05rem !important;
        }
        .sdc-devis-right {
            padding: 24px 18px 20px 18px !important;
        }
        .sdc-devis-dots {
            margin-bottom: 22px !important;
        }
        .sdc-devis-overlay h3.sdc-devis-step-title {
            font-size: 1.2rem !important;
            margin: 0 0 24px 0 !important;
        }
        .sdc-devis-input,
        .sdc-devis-textarea {
            padding: 11px 14px !important;
            font-size: 0.88rem !important;
            border-radius: 10px !important;
        }
        .sdc-devis-textarea {
            min-height: 64px !important;
            height: 64px !important;
        }
        .sdc-devis-overlay button.sdc-chip {
            font-size: 0.82rem !important;
            padding: 8px 14px !important;
        }
        .sdc-services-grid,
        .sdc-sub-services-grid,
        .sdc-sub-services-group-grid {
            gap: 8px !important;
            margin-top: 6px !important;
            margin-bottom: 6px !important;
        }
        .sdc-sub-service-group-title {
            margin: 8px 0 4px 0 !important;
            font-size: 0.72rem !important;
        }
        .sdc-devis-overlay .sdc-devis-btn-primary {
            min-width: 155px !important;
            height: 46px !important;
            padding: 0 16px 0 50px !important;
            font-size: 13.5px !important;
            border-radius: 12px !important;
        }
        .sdc-devis-overlay .sdc-devis-btn-primary::before {
            height: 38px !important;
            width: 38px !important;
            left: 4px !important;
            top: 4px !important;
            border-radius: 8px !important;
        }
        .sdc-devis-overlay .sdc-devis-btn-primary::after {
            width: 16px !important;
            height: 16px !important;
            left: 15px !important;
        }
        .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary {
            padding: 0 50px 0 16px !important;
        }
        .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary::after {
            left: auto !important;
            right: 15px !important;
        }
    }
    </style>


    <!-- ════════════════════════════════════════════════════════════
         SDC Devis Popup — HTML
         ════════════════════════════════════════════════════════════ -->
    <div class="sdc-devis-overlay" id="sdc-devis-overlay" dir="<?php echo $dir; ?>">
        <div class="sdc-devis-popup">

            <!-- Close Button -->
            <button class="sdc-devis-close" id="sdc-devis-close" aria-label="<?php echo $close_label; ?>">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L13 13M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- ═══ LEFT PANEL ═══ -->
            <div class="sdc-devis-left" id="sdc-devis-left">
                <div class="sdc-devis-left-content-wrap">
                    <!-- Top Title -->
                    <h2 class="sdc-devis-left-title">
                        <?php echo $panel_title_p1; ?>
                        <span class="sdc-highlight">
                            <?php echo $panel_title_hl; ?>
                            <svg class="sdc-underline-curve" viewBox="0 0 100 12" preserveAspectRatio="none">
                                <path d="M3 8 Q 50 3, 97 8" stroke="#FF8C61" stroke-width="2" fill="none" stroke-linecap="round"/>
                                <path d="M8 11 Q 50 6, 92 11" stroke="#FF8C61" stroke-width="2" fill="none" stroke-linecap="round"/>
                            </svg>
                        </span> <br class="sdc-br-desktop">
                        <?php echo $panel_title_p2; ?>
                    </h2>

                    <!-- Middle Checklist -->
                    <div class="sdc-devis-left-middle">
                        <ul class="sdc-devis-checks">
                            <li>
                                <svg class="sdc-check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span><?php echo $check1; ?></span>
                            </li>
                            <li>
                                <svg class="sdc-check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span><?php echo $check2; ?></span>
                            </li>
                            <li>
                                <svg class="sdc-check-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span><?php echo $check3; ?></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Bottom Contact Info -->
                    <div class="sdc-devis-left-contact">
                        <span class="sdc-devis-contact-label"><?php echo $contact_label; ?></span>
                        <a href="mailto:kmlmes13@gmail.com" class="sdc-devis-contact-email">kmlmes13@gmail.com</a>
                    </div>
                </div>
            </div>

            <!-- ═══ RIGHT PANEL ═══ -->
            <div class="sdc-devis-right">

                <!-- Step Indicator Dots -->
                <div class="sdc-devis-dots" id="sdc-devis-dots">
                    <span class="sdc-dot sdc-active"></span>
                    <span class="sdc-dot"></span>
                </div>

                <!-- ── STEP 1: Service Selection ── -->
                <div class="sdc-devis-step sdc-active" id="sdc-step-1">
                    <h3 class="sdc-devis-step-title"><?php echo $step1_title; ?></h3>

                    <div class="sdc-devis-field" id="sdc-field-service">
                        <label class="sdc-devis-label"><?php echo $service_label; ?></label>
                        <div class="sdc-services-grid">
                            <?php foreach ( $services[ $lang ] as $idx => $s ) : ?>
                                <button type="button" class="sdc-chip sdc-service-chip" data-value="<?php echo $idx; ?>">
                                    <?php echo esc_html( $s['label'] ); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="sdc-service" value="" />
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>

                    <div class="sdc-devis-field sdc-devis-field-sub" id="sdc-sub-field">
                        <label class="sdc-devis-label"><?php echo $sub_service_label; ?></label>
                        <div class="sdc-sub-services-grid" id="sdc-sub-services-container">
                            <!-- Populated dynamically via JavaScript -->
                        </div>
                        <input type="hidden" id="sdc-sub-service" value="" />
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>

                    <div class="sdc-devis-actions">
                        <div></div>
                        <button class="sdc-devis-btn-primary" id="sdc-btn-next" type="button">
                            <span class="sdc-btn-text"><?php echo $btn_next; ?></span>
                        </button>
                    </div>
                </div>

                <!-- ── STEP 2: Contact Info ── -->
                <div class="sdc-devis-step" id="sdc-step-2">
                    <h3 class="sdc-devis-step-title"><?php echo $step2_title; ?></h3>

                    <div class="sdc-devis-field" id="sdc-field-name">
                        <label class="sdc-devis-label" for="sdc-fullname"><?php echo $name_label; ?></label>
                        <input type="text" class="sdc-devis-input" id="sdc-fullname" placeholder="<?php echo esc_attr( $name_placeholder ); ?>" />
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>
                    <div class="sdc-devis-field" id="sdc-field-email">
                        <label class="sdc-devis-label" for="sdc-email"><?php echo $email_label; ?></label>
                        <input type="email" class="sdc-devis-input" id="sdc-email" placeholder="<?php echo esc_attr( $email_placeholder ); ?>" />
                        <span class="sdc-devis-error-msg"><?php echo $err_email; ?></span>
                    </div>

                    <div class="sdc-devis-field" id="sdc-field-phone">
                        <label class="sdc-devis-label" for="sdc-phone"><?php echo $phone_label; ?></label>
                        <input type="tel" class="sdc-devis-input" id="sdc-phone" placeholder="<?php echo esc_attr( $phone_placeholder ); ?>" />
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>

                    <div class="sdc-devis-field" id="sdc-field-brief">
                        <label class="sdc-devis-label" for="sdc-brief"><?php echo $brief_label; ?></label>
                        <textarea class="sdc-devis-textarea" id="sdc-brief" rows="3" placeholder="<?php echo esc_attr( $brief_placeholder ); ?>"></textarea>
                    </div>

                    <div class="sdc-devis-actions">
                        <button class="sdc-devis-btn-back" id="sdc-btn-back" type="button">
                            <span class="sdc-btn-arrow"><?php echo $arrow_back; ?></span>
                            <?php echo $btn_back; ?>
                        </button>
                        <button class="sdc-devis-btn-primary" id="sdc-btn-submit" type="button">
                            <span class="sdc-btn-text"><?php echo $btn_submit; ?></span>
                            <span class="sdc-devis-spinner"></span>
                        </button>
                    </div>

                    <p class="sdc-devis-submit-error" id="sdc-submit-error" style="display:none;"></p>
                </div>

                <!-- ── SUCCESS STATE ── -->
                <div class="sdc-devis-success" id="sdc-devis-success">
                    <div class="sdc-devis-success-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 16L14 22L24 10" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="sdc-devis-success-title"><?php echo $success_title; ?></h3>
                    <p class="sdc-devis-success-msg"><?php echo $success_msg; ?></p>
                </div>

            </div><!-- /.sdc-devis-right -->
        </div><!-- /.sdc-devis-popup -->
    </div><!-- /.sdc-devis-overlay -->


    <!-- ════════════════════════════════════════════════════════════
         SDC Devis Popup — JavaScript
         ════════════════════════════════════════════════════════════ -->
    <script>
    (function() {

        /* ── Config (from PHP) ── */
        var sdcServices = <?php echo $services_json; ?>;
        var sdcConfig = {
            ajaxUrl:    '<?php echo esc_url( $ajax_url ); ?>',
            nonce:      '<?php echo esc_attr( $nonce ); ?>',
            errGeneric: '<?php echo esc_js( $err_generic ); ?>'
        };

        /* ── Elements ── */
        var overlay     = document.getElementById('sdc-devis-overlay');
        var closeBtn    = document.getElementById('sdc-devis-close');
        var step1       = document.getElementById('sdc-step-1');
        var step2       = document.getElementById('sdc-step-2');
        var successEl   = document.getElementById('sdc-devis-success');
        var dots        = document.querySelectorAll('#sdc-devis-dots .sdc-dot');

        var serviceSel  = document.getElementById('sdc-service');
        var subSel      = document.getElementById('sdc-sub-service');
        var subField    = document.getElementById('sdc-sub-field');

        var nameInput   = document.getElementById('sdc-fullname');
        var emailInput  = document.getElementById('sdc-email');
        var phoneInput  = document.getElementById('sdc-phone');
        var briefInput  = document.getElementById('sdc-brief');

        var nextBtn     = document.getElementById('sdc-btn-next');
        var backBtn     = document.getElementById('sdc-btn-back');
        var submitBtn   = document.getElementById('sdc-btn-submit');
        var submitErr   = document.getElementById('sdc-submit-error');

        if (!overlay || !closeBtn) return;

        var isRTL = overlay.getAttribute('dir') === 'rtl';

        /* ────────────────────────────────────────────────────────
           Open / Close (Body Scroll Lock)
           ──────────────────────────────────────────────────────── */
        var scrollY = 0;

        function openPopup() {
            overlay.classList.add('sdc-active');
            scrollY = window.pageYOffset || document.documentElement.scrollTop;
            document.body.style.position = 'fixed';
            document.body.style.top = '-' + scrollY + 'px';
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        }

        function closePopup() {
            overlay.classList.remove('sdc-active');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            window.scrollTo(0, scrollY);
            setTimeout(resetForm, 450);
        }

        function resetForm() {
            /* Back to step 1 */
            step1.classList.add('sdc-active');
            step1.classList.remove('sdc-slide-in-left', 'sdc-slide-in-right');
            step2.classList.remove('sdc-active', 'sdc-slide-in-left', 'sdc-slide-in-right');
            successEl.classList.remove('sdc-active');

            dots[0].classList.add('sdc-active');
            dots[1].classList.remove('sdc-active');

            /* Clear values */
            serviceSel.value = '';
            subSel.value = '';
            
            // Reset chips
            var serviceChips = overlay.querySelectorAll('.sdc-service-chip');
            for (var s = 0; s < serviceChips.length; s++) {
                serviceChips[s].classList.remove('sdc-selected');
            }
            document.getElementById('sdc-sub-services-container').innerHTML = '';
            subField.classList.remove('sdc-visible');
            
            // Restore expanded dark panel on mobile
            var leftPanel = document.querySelector('.sdc-devis-left');
            if (leftPanel) leftPanel.classList.remove('sdc-left-collapsed');
            
            nameInput.value = '';
            emailInput.value = '';
            phoneInput.value = '';
            briefInput.value = '';

            clearErrors();
        }

        /* ────────────────────────────────────────────────────────
           Errors
           ──────────────────────────────────────────────────────── */
        function clearErrors() {
            var errs = overlay.querySelectorAll('.sdc-error');
            for (var i = 0; i < errs.length; i++) errs[i].classList.remove('sdc-error');
            if (submitErr) submitErr.style.display = 'none';
        }

        function setError(fieldId) {
            var el = document.getElementById(fieldId);
            if (el) el.classList.add('sdc-error');
        }

        /* ────────────────────────────────────────────────────────
           Service → Sub-service
           ──────────────────────────────────────────────────────── */
        /* ── Service Chip Click Logic (Multi-select) ── */
        var serviceChips = overlay.querySelectorAll('.sdc-service-chip');
        for (var i = 0; i < serviceChips.length; i++) {
            serviceChips[i].addEventListener('click', function() {
                clearErrors();
                
                // Toggle selection
                this.classList.toggle('sdc-selected');
                
                // Collect selected service indices
                var selectedIndices = [];
                var activeServiceChips = overlay.querySelectorAll('.sdc-service-chip.sdc-selected');
                for (var c = 0; c < activeServiceChips.length; c++) {
                    selectedIndices.push(parseInt(activeServiceChips[c].getAttribute('data-value'), 10));
                }
                
                serviceSel.value = selectedIndices.join(',');
                
                // Animate the mobile left panel: collapse after first selection, expand if all deselected
                var leftPanel = document.querySelector('.sdc-devis-left');
                if (leftPanel) {
                    if (selectedIndices.length > 0) {
                        leftPanel.classList.add('sdc-left-collapsed');
                    } else {
                        leftPanel.classList.remove('sdc-left-collapsed');
                    }
                }
                
                // Clear sub-service selection
                subSel.value = '';
                var subContainer = document.getElementById('sdc-sub-services-container');
                subContainer.innerHTML = '';
                
                if (selectedIndices.length === 0) {
                    subField.classList.remove('sdc-visible');
                    return;
                }
                
                // Populate sub-service chips grouped by service category
                for (var s = 0; s < selectedIndices.length; s++) {
                    var idx = selectedIndices[s];
                    if (sdcServices[idx]) {
                        var subs = sdcServices[idx].subs;
                        
                        // Category Header/Separator
                        var groupTitle = document.createElement('div');
                        groupTitle.className = 'sdc-sub-service-group-title';
                        groupTitle.textContent = sdcServices[idx].label;
                        subContainer.appendChild(groupTitle);
                        
                        // Group Grid container
                        var groupGrid = document.createElement('div');
                        groupGrid.className = 'sdc-sub-services-group-grid';
                        subContainer.appendChild(groupGrid);
                        
                        for (var k = 0; k < subs.length; k++) {
                            var btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'sdc-chip sdc-sub-service-chip';
                            btn.textContent = subs[k];
                            btn.setAttribute('data-value', subs[k]);
                            
                            btn.addEventListener('click', function() {
                                clearErrors();
                                this.classList.toggle('sdc-selected');
                                
                                var activeSubChips = subContainer.querySelectorAll('.sdc-sub-service-chip.sdc-selected');
                                var subVals = [];
                                for (var m = 0; m < activeSubChips.length; m++) {
                                    subVals.push(activeSubChips[m].getAttribute('data-value'));
                                }
                                subSel.value = subVals.join(', ');
                            });
                            
                            groupGrid.appendChild(btn);
                        }
                    }
                }
                
                subField.classList.add('sdc-visible');
            });
        }

        /* ────────────────────────────────────────────────────────
           Step Navigation
           ──────────────────────────────────────────────────────── */
        nextBtn.addEventListener('click', function() {
            clearErrors();
            var valid = true;

            if (!serviceSel.value) {
                setError('sdc-field-service');
                valid = false;
            }

            if (subField.classList.contains('sdc-visible') && !subSel.value) {
                setError('sdc-sub-field');
                valid = false;
            }

            if (!valid) return;

            /* Transition → Step 2 */
            step1.classList.remove('sdc-active');
            step2.classList.add('sdc-active');
            step2.classList.remove('sdc-slide-in-left', 'sdc-slide-in-right');
            step2.classList.add(isRTL ? 'sdc-slide-in-left' : 'sdc-slide-in-right');
            setTimeout(function() {
                step2.classList.remove('sdc-slide-in-left', 'sdc-slide-in-right');
            }, 420);

            var rightPanel = document.querySelector('.sdc-devis-right');
            if (rightPanel) {
                rightPanel.scrollTop = 0;
            }

            dots[0].classList.remove('sdc-active');
            dots[1].classList.add('sdc-active');
        });

        backBtn.addEventListener('click', function() {
            clearErrors();

            /* Transition → Step 1 */
            step2.classList.remove('sdc-active');
            step1.classList.add('sdc-active');
            step1.classList.remove('sdc-slide-in-left', 'sdc-slide-in-right');
            step1.classList.add(isRTL ? 'sdc-slide-in-right' : 'sdc-slide-in-left');
            setTimeout(function() {
                step1.classList.remove('sdc-slide-in-left', 'sdc-slide-in-right');
            }, 420);

            var rightPanel = document.querySelector('.sdc-devis-right');
            if (rightPanel) {
                rightPanel.scrollTop = 0;
            }

            dots[1].classList.remove('sdc-active');
            dots[0].classList.add('sdc-active');
        });

        /* ────────────────────────────────────────────────────────
           Submit
           ──────────────────────────────────────────────────────── */
        submitBtn.addEventListener('click', function() {
            clearErrors();
            var valid = true;

            if (!nameInput.value.trim()) {
                setError('sdc-field-name');
                valid = false;
            }
            if (!emailInput.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                setError('sdc-field-email');
                valid = false;
            }
            if (!phoneInput.value.trim()) {
                setError('sdc-field-phone');
                valid = false;
            }

            if (!valid) return;

            /* Loading */
            submitBtn.classList.add('sdc-loading');
            submitBtn.disabled = true;

            /* Gather data */
            var svcIdxVal = serviceSel.value; // e.g., "0,1"
            var svcLabels = [];
            if (svcIdxVal) {
                var idxs = svcIdxVal.split(',');
                for (var s = 0; s < idxs.length; s++) {
                    var idx = parseInt(idxs[s], 10);
                    if (sdcServices[idx]) {
                        svcLabels.push(sdcServices[idx].label);
                    }
                }
            }
            var svcLabel = svcLabels.join(', ');

            var fd = new FormData();
            fd.append('action',      'sdc_devis_submit');
            fd.append('sdc_nonce',   sdcConfig.nonce);
            fd.append('service',     svcLabel);
            fd.append('sub_service', subSel.value);
            fd.append('fullname',    nameInput.value.trim());
            fd.append('email',       emailInput.value.trim());
            fd.append('phone',       phoneInput.value.trim());
            fd.append('brief',       briefInput.value.trim());

            fetch(sdcConfig.ajaxUrl, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                submitBtn.classList.remove('sdc-loading');
                submitBtn.disabled = false;

                if (data.success) {
                    successEl.classList.add('sdc-active');
                    setTimeout(closePopup, 4000);
                } else {
                    submitErr.textContent = (data.data && data.data.message) ? data.data.message : sdcConfig.errGeneric;
                    submitErr.style.display = 'block';
                }
            })
            .catch(function() {
                submitBtn.classList.remove('sdc-loading');
                submitBtn.disabled = false;
                submitErr.textContent = sdcConfig.errGeneric;
                submitErr.style.display = 'block';
            });
        });

        /* ────────────────────────────────────────────────────────
           Triggers — Open
           ──────────────────────────────────────────────────────── */
        var openBtn = document.getElementById('sdc-open-popup');
        if (openBtn) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openPopup();
            });
        }

        var allTriggers = document.querySelectorAll('.sdc-open-devis');
        for (var t = 0; t < allTriggers.length; t++) {
            allTriggers[t].addEventListener('click', function(e) {
                e.preventDefault();
                openPopup();
            });
        }

        /* ────────────────────────────────────────────────────────
           Triggers — Close
           ──────────────────────────────────────────────────────── */
        closeBtn.addEventListener('click', closePopup);

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closePopup();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('sdc-active')) {
                closePopup();
            }
        });



        /* ────────────────────────────────────────────────────────
           Clear field error on interaction
           ──────────────────────────────────────────────────────── */
        var allInputs = overlay.querySelectorAll('.sdc-devis-input, .sdc-devis-textarea, .sdc-devis-select');
        for (var k = 0; k < allInputs.length; k++) {
            allInputs[k].addEventListener('focus', function() {
                var parent = this.closest('.sdc-devis-field');
                if (parent) parent.classList.remove('sdc-error');
            });
            allInputs[k].addEventListener('change', function() {
                var parent = this.closest('.sdc-devis-field');
                if (parent) parent.classList.remove('sdc-error');
            });
        }

    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'sdc_devis_popup_render' );

