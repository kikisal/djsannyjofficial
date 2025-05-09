
((m) => {

    class PrivacyPolicyPage {

        constructor(elementQuery) {
            this._targetElement = domSelect(elementQuery);

            this._page           = document.createElement('div');
            this._page.innerHTML = PRIVACY_POLICY_HTML;

        }
        
        onPageSwitch() {
            console.log("switched");
        }

        onPageActive() {
            this._targetElement.appendChild(this._page);
        }
        
        static create(elementQuery) {
            return new PrivacyPolicyPage(elementQuery);
        }
    }

    m.PrivacyPolicyPage = PrivacyPolicyPage;

    const PRIVACY_POLICY_HTML = `
    <div class="over" id="over"  style="margin-top: 100px">
        <div class="overlay" --overlay>
            <div class="new desc" --content>
                <div class="container">
        <h1>Privacy Policy</h1>

        <p><strong>Last updated:</strong> 10/25/2024</p>

        <p>This Privacy Policy informs you of our policies regarding the usage of the App.</p>

        <h3 style="margin-top: 20px; margin-bottom: 4px;">Information Collection and Use</h3>
        <p>We want to inform you that we do not collect, store, or use any personal data from users.</p>

        <h3 style="margin-top: 20px; margin-bottom: 4px;">Data Sharing</h3>
        <p>Since we do not collect any personal data, we do not share any data with third parties.</p>

        <h3 style="margin-top: 20px; margin-bottom: 4px;">Links to Other Sites</h3>
        <p>Our App may contain links to third-party sites that are not operated by us. We strongly advise you to review the Privacy Policy of every site you visit.</p>

        <h3 style="margin-top: 20px; margin-bottom: 4px;">Changes to This Privacy Policy</h3>
        <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>

        <h3 style="margin-top: 20px; margin-bottom: 4px;">Contact Us</h3>
        <p>If you have any questions about this Privacy Policy, please contact us at: dashgogogovia@gmail.com</p>

        <div class="footer">
            <p>&copy; 2024 Radio Generation App</p>
        </div>
    </div>
            </div>
        </div>
    </div>
    `;
    
})(window);