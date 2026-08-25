{{-- Shared Privacy Policy modal for auth pages (content aligned with https://www.marinecaddie.com/privacy-policy) --}}
<style>
    .mc-privacy-link {
        color: inherit;
        text-decoration: underline;
        cursor: pointer;
    }

    .mc-privacy-link:hover {
        color: #38bdf8;
    }

    .mc-privacy-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10050;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(14, 29, 74, 0.55);
    }

    .mc-privacy-modal.is-open {
        display: flex;
    }

    .mc-privacy-dialog {
        width: 100%;
        max-width: 640px;
        max-height: min(85vh, 720px);
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 24px 48px rgba(14, 29, 74, 0.28);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .mc-privacy-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .mc-privacy-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #0E1D4A;
    }

    .mc-privacy-close {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }

    .mc-privacy-close:hover {
        background: #e2e8f0;
        color: #0E1D4A;
    }

    .mc-privacy-body {
        padding: 20px 22px 8px;
        overflow-y: auto;
        color: #334155;
        font-size: 13px;
        line-height: 1.65;
    }

    .mc-privacy-body h3 {
        margin: 18px 0 8px;
        font-size: 14px;
        font-weight: 700;
        color: #0E1D4A;
    }

    .mc-privacy-body h3:first-child {
        margin-top: 0;
    }

    .mc-privacy-body p {
        margin: 0 0 10px;
    }

    .mc-privacy-body ul {
        margin: 0 0 12px;
        padding-left: 18px;
    }

    .mc-privacy-body li {
        margin-bottom: 6px;
    }

    .mc-privacy-body a {
        color: #349DDA;
        font-weight: 600;
    }

    .mc-privacy-footer {
        padding: 14px 22px 18px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fff;
    }

    .mc-privacy-btn {
        height: 38px;
        padding: 0 16px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(135deg, #FF5A5F 0%, #ff7a7e 100%);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 8px 16px rgba(255, 90, 95, 0.22);
    }

    .mc-privacy-btn:hover {
        opacity: 0.95;
    }

    body.mc-privacy-open {
        overflow: hidden;
    }
</style>

<div
    id="mc-privacy-modal"
    class="mc-privacy-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="mc-privacy-title"
    hidden
>
    <div class="mc-privacy-dialog" role="document">
        <div class="mc-privacy-header">
            <h2 id="mc-privacy-title">Privacy Policy</h2>
            <button type="button" class="mc-privacy-close" id="mc-privacy-close" aria-label="Close privacy policy">&times;</button>
        </div>

        <div class="mc-privacy-body">
            <h3>Introduction</h3>
            <p>
                MarineCaddie Shipping (“MarineCaddie,” “we,” “us,” or “our”) respects your privacy.
                This Privacy Policy explains how we collect, use, and protect personal information when you visit our website,
                use the MarineCaddie Portal, contact our team, or engage our marine logistics and freight forwarding services.
            </p>
            <p>
                By using our website, portal, or submitting information to us, you acknowledge the practices described in this policy.
                If you do not agree, please discontinue use and contact us at
                <a href="mailto:ops@marinecaddie.com">ops@marinecaddie.com</a>.
            </p>

            <h3>Information we collect</h3>
            <p>
                We may collect information you provide directly, such as your name, company, email address, phone number,
                and message content when you request a quote, subscribe to updates, sign in to the portal, or ask for support.
                We may also collect limited technical data such as browser type, device information, and pages visited
                to help us improve site performance and security.
            </p>
            <ul>
                <li>Contact and inquiry details submitted through forms</li>
                <li>Account and login details used to access the MarineCaddie Portal</li>
                <li>Business context shared for logistics or maritime service discussions</li>
                <li>Website and portal usage / diagnostic information</li>
            </ul>
            <p>
                We do not knowingly collect sensitive personal data through this website unless you choose to provide it
                in a message or attachment.
            </p>

            <h3>How we use information</h3>
            <p>
                We use collected information to respond to inquiries, provide and improve our services, authenticate portal access,
                communicate about engagements, maintain website and portal security, and meet legal or compliance obligations
                related to our business operations.
            </p>
            <p>
                We do not sell personal information. We may share information with trusted service providers who support hosting,
                communications, or analytics, subject to confidentiality obligations, or when required by law.
            </p>

            <h3>Your choices and contact</h3>
            <p>
                You may request access, correction, or deletion of personal information we hold about you, subject to applicable law.
                For privacy requests, email
                <a href="mailto:ops@marinecaddie.com">ops@marinecaddie.com</a>
                or call
                <a href="tel:+971505643375">+971 50 5643375</a>.
            </p>
            <p>
                We may update this policy from time to time. The revised version will be posted on
                <a href="https://www.marinecaddie.com/privacy-policy" target="_blank" rel="noopener noreferrer">marinecaddie.com/privacy-policy</a>
                and reflected in this notice.
            </p>
        </div>

        <div class="mc-privacy-footer">
            <button type="button" class="mc-privacy-btn" id="mc-privacy-done">Close</button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('mc-privacy-modal');
    if (!modal) {
        return;
    }

    var closeBtn = document.getElementById('mc-privacy-close');
    var doneBtn = document.getElementById('mc-privacy-done');

    function openModal(e) {
        if (e) {
            e.preventDefault();
        }
        modal.hidden = false;
        modal.classList.add('is-open');
        document.body.classList.add('mc-privacy-open');
        if (closeBtn) {
            closeBtn.focus();
        }
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.hidden = true;
        document.body.classList.remove('mc-privacy-open');
    }

    document.querySelectorAll('[data-mc-privacy-open]').forEach(function (link) {
        link.addEventListener('click', openModal);
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (doneBtn) {
        doneBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
</script>
