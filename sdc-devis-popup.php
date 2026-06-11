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
        . '<h2 style="color:#F46036;margin:0;font-size:20px;">📋 Nouvelle Demande de Devis</h2>'
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
        . '<td style="padding:12px 0;border-bottom:1px solid #f0f0f0;color:#1a1a1a;font-weight:600;"><a href="mailto:' . esc_attr( $email ) . '" style="color:#F46036;">' . esc_html( $email ) . '</a></td></tr>'
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
    $panel_title    = $is_arabic ? 'لديك فكرة مشروع؟'     : 'Vous avez un projet en tête ?';
    $panel_subtitle = $is_arabic ? 'لنبدأ معاً.'           : 'Commençons ensemble.';
    $check1         = $is_arabic ? 'رد سريع خلال 24 ساعة'  : 'Réponse sous 24h';
    $check2         = $is_arabic ? 'استشارة أولية مجانية'   : 'Consultation initiale gratuite';
    $check3         = $is_arabic ? 'فريق متخصص لمشروعك'    : 'Équipe dédiée à votre projet';
    $contact_label  = $is_arabic ? 'راسلنا مباشرة :'        : 'Écrivez-nous directement :';

    // ── Step 1 ──
    $step1_title             = $is_arabic ? 'اختر خدمتك'            : 'Choisissez votre service';
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
        direction: ltr;
        max-width: 880px;
        width: 92%;
        max-height: 90vh;
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
    .sdc-devis-close {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 10;
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.7);
        cursor: pointer;
        transition: background 0.25s ease, color 0.25s ease;
    }
    .sdc-devis-close:hover {
        background: rgba(255,255,255,0.2);
        color: #ffffff;
    }

    /* ═══ LEFT PANEL ═══════════════════════════════════════════════ */
    .sdc-devis-left {
        flex: 0 0 40%;
        background: #1a1a1a;
        background-image: radial-gradient(circle at 20% 80%, rgba(244, 96, 54, 0.06) 0%, transparent 50%);
        padding: 48px 36px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }
    .sdc-devis-left-title {
        font-family: inherit;
        font-size: 1.55rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.35;
        margin-bottom: 6px;
    }
    .sdc-devis-left-title span {
        color: #F46036;
    }
    .sdc-devis-left-subtitle {
        font-family: inherit;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.55);
        line-height: 1.5;
        margin-bottom: 36px;
    }

    /* Checkmarks */
    .sdc-devis-checks {
        list-style: none;
        margin: 0 0 36px 0;
        padding: 0;
    }
    .sdc-devis-checks li {
        display: flex;
        align-items: center;
        gap: 14px;
        font-family: inherit;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.8);
        line-height: 1.5;
        margin-bottom: 18px;
    }
    .sdc-devis-checks li:last-child { margin-bottom: 0; }

    .sdc-check-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(244, 96, 54, 0.15);
        color: #F46036;
        font-size: 0.7rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* Contact info */
    .sdc-devis-left-contact {
        margin-top: auto;
        padding-top: 24px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .sdc-devis-contact-label {
        display: block;
        font-family: inherit;
        font-size: 0.78rem;
        color: rgba(255,255,255,0.4);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .sdc-devis-contact-email {
        color: #F46036;
        text-decoration: none;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 500;
        transition: opacity 0.2s ease;
    }
    .sdc-devis-contact-email:hover {
        opacity: 0.75;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    /* ═══ RIGHT PANEL ═════════════════════════════════════════════ */
    .sdc-devis-right {
        flex: 1;
        padding: 44px 40px;
        overflow-y: auto;
        position: relative;
        min-height: 420px;
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
        background: #F46036;
    }

    /* ── Step Title ── */
    .sdc-devis-step-title {
        font-family: inherit;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 28px;
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

    /* Select */
    .sdc-devis-select {
        width: 100%;
        padding: 13px 40px 13px 18px;
        font-family: inherit;
        font-size: 0.92rem;
        color: #1a1a1a;
        background-color: #ffffff;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%23999' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 12px 8px;
        cursor: pointer;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .sdc-devis-select:focus {
        border-color: #F46036;
        box-shadow: 0 0 0 3px rgba(244, 96, 54, 0.08);
    }

    /* Input */
    .sdc-devis-input {
        width: 100%;
        padding: 13px 18px;
        font-family: inherit;
        font-size: 0.92rem;
        color: #1a1a1a;
        background: #ffffff;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .sdc-devis-input:focus {
        border-color: #F46036;
        box-shadow: 0 0 0 3px rgba(244, 96, 54, 0.08);
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
        background: #ffffff;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        resize: vertical;
        min-height: 90px;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .sdc-devis-textarea:focus {
        border-color: #F46036;
        box-shadow: 0 0 0 3px rgba(244, 96, 54, 0.08);
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
        transition: max-height 0.4s ease, opacity 0.35s ease 0.05s, margin-bottom 0.3s ease;
    }
    .sdc-devis-field-sub.sdc-visible {
        max-height: 120px;
        opacity: 1;
        margin-bottom: 20px;
    }

    /* ── Error State ── */
    .sdc-devis-field.sdc-error .sdc-devis-select,
    .sdc-devis-field.sdc-error .sdc-devis-input,
    .sdc-devis-field.sdc-error .sdc-devis-textarea {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.08);
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
    .sdc-devis-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 30px;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        color: #ffffff;
        background: #F46036;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
    }
    .sdc-devis-btn-primary:hover {
        background: #e04f28;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(244, 96, 54, 0.3);
    }
    .sdc-devis-btn-primary:active {
        transform: translateY(0);
    }

    /* Loading state */
    .sdc-devis-btn-primary.sdc-loading {
        pointer-events: none;
        opacity: 0.8;
    }
    .sdc-devis-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: sdcSpin 0.6s linear infinite;
    }
    .sdc-devis-btn-primary.sdc-loading .sdc-devis-spinner { display: inline-block; }
    .sdc-devis-btn-primary.sdc-loading .sdc-btn-arrow { display: none; }

    /* Back Button */
    .sdc-devis-btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: none;
        border: none;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 500;
        color: #999;
        cursor: pointer;
        padding: 8px 4px;
        transition: color 0.25s ease;
    }
    .sdc-devis-btn-back:hover {
        color: #F46036;
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
    .sdc-devis-btn-back:hover .sdc-btn-arrow {
        transform: translateX(-3px);
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
        background: linear-gradient(135deg, #F46036, #e04f28);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        animation: sdcScaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s both;
        box-shadow: 0 8px 24px rgba(244, 96, 54, 0.3);
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
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-close {
        right: auto;
        left: 16px;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-left,
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-right {
        direction: rtl;
        text-align: right;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-select {
        background-position: left 16px center;
        padding: 13px 18px 13px 40px;
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-primary:hover .sdc-btn-arrow {
        transform: translateX(-3px);
    }
    .sdc-devis-overlay[dir="rtl"] .sdc-devis-btn-back:hover .sdc-btn-arrow {
        transform: translateX(3px);
    }

    /* ═══ RESPONSIVE ═════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .sdc-devis-popup {
            flex-direction: column;
            width: 95%;
            max-height: 92vh;
            border-radius: 16px;
        }
        .sdc-devis-left {
            flex: none;
            padding: 28px 28px 24px;
        }
        .sdc-devis-left-title { font-size: 1.25rem; }
        .sdc-devis-left-subtitle { margin-bottom: 0; display: none; }
        .sdc-devis-checks { display: none; }
        .sdc-devis-left-contact { display: none; }
        .sdc-devis-close {
            background: rgba(255,255,255,0.15);
        }
        .sdc-devis-right {
            padding: 28px 24px;
            min-height: auto;
        }
        .sdc-devis-row {
            flex-direction: column;
            gap: 0;
        }
        .sdc-devis-step-title { font-size: 1.05rem; }
    }

    @media (max-width: 480px) {
        .sdc-devis-popup {
            width: 100%;
            max-height: 100vh;
            height: 100%;
            border-radius: 0;
        }
        .sdc-devis-left { padding: 20px 20px 16px; }
        .sdc-devis-left-title { font-size: 1.1rem; }
        .sdc-devis-right { padding: 24px 20px; }
        .sdc-devis-select,
        .sdc-devis-input,
        .sdc-devis-textarea {
            font-size: 0.88rem;
            padding: 12px 16px;
        }
        .sdc-devis-select { padding-right: 36px; }
        .sdc-devis-overlay[dir="rtl"] .sdc-devis-select {
            padding: 12px 16px 12px 36px;
        }
        .sdc-devis-btn-primary { padding: 12px 24px; font-size: 0.85rem; }
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
            <div class="sdc-devis-left">
                <div class="sdc-devis-left-inner">
                    <h2 class="sdc-devis-left-title"><?php echo $panel_title; ?></h2>
                    <p class="sdc-devis-left-subtitle"><?php echo $panel_subtitle; ?></p>

                    <ul class="sdc-devis-checks">
                        <li><span class="sdc-check-icon">✓</span> <?php echo $check1; ?></li>
                        <li><span class="sdc-check-icon">✓</span> <?php echo $check2; ?></li>
                        <li><span class="sdc-check-icon">✓</span> <?php echo $check3; ?></li>
                    </ul>

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
                        <label class="sdc-devis-label" for="sdc-service"><?php echo $service_label; ?></label>
                        <select class="sdc-devis-select" id="sdc-service">
                            <option value=""><?php echo $service_placeholder; ?></option>
                            <?php foreach ( $services[ $lang ] as $idx => $s ) : ?>
                                <option value="<?php echo $idx; ?>"><?php echo esc_html( $s['label'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>

                    <div class="sdc-devis-field sdc-devis-field-sub" id="sdc-sub-field">
                        <label class="sdc-devis-label" for="sdc-sub-service"><?php echo $sub_service_label; ?></label>
                        <select class="sdc-devis-select" id="sdc-sub-service" data-placeholder="<?php echo esc_attr( $sub_service_placeholder ); ?>">
                            <option value=""><?php echo $sub_service_placeholder; ?></option>
                        </select>
                        <span class="sdc-devis-error-msg"><?php echo $err_required; ?></span>
                    </div>

                    <div class="sdc-devis-actions">
                        <div></div>
                        <button class="sdc-devis-btn-primary" id="sdc-btn-next" type="button">
                            <?php echo $btn_next; ?>
                            <span class="sdc-btn-arrow"><?php echo $arrow_next; ?></span>
                        </button>
                    </div>
                </div>

                <!-- ── STEP 2: Contact Info ── -->
                <div class="sdc-devis-step" id="sdc-step-2">
                    <h3 class="sdc-devis-step-title"><?php echo $step2_title; ?></h3>

                    <div class="sdc-devis-row">
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
                            <?php echo $btn_submit; ?>
                            <span class="sdc-btn-arrow"><?php echo $arrow_next; ?></span>
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
           Open / Close
           ──────────────────────────────────────────────────────── */
        function openPopup() {
            overlay.classList.add('sdc-active');
            document.body.style.overflow = 'hidden';
        }

        function closePopup() {
            overlay.classList.remove('sdc-active');
            document.body.style.overflow = '';
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
            subSel.innerHTML = '<option value="">' + subSel.getAttribute('data-placeholder') + '</option>';
            subField.classList.remove('sdc-visible');
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
        serviceSel.addEventListener('change', function() {
            clearErrors();
            var idx = parseInt(this.value, 10);

            if (isNaN(idx) || !sdcServices[idx]) {
                subField.classList.remove('sdc-visible');
                return;
            }

            var subs = sdcServices[idx].subs;
            var ph   = subSel.getAttribute('data-placeholder');
            subSel.innerHTML = '<option value="">' + ph + '</option>';

            for (var i = 0; i < subs.length; i++) {
                var opt = document.createElement('option');
                opt.value = subs[i];
                opt.textContent = subs[i];
                subSel.appendChild(opt);
            }

            subField.classList.add('sdc-visible');
        });

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
            var svcIdx   = parseInt(serviceSel.value, 10);
            var svcLabel = sdcServices[svcIdx] ? sdcServices[svcIdx].label : '';

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
