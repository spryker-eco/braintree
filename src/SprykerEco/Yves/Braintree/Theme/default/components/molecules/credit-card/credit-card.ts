declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

const NONCE_INPUT_NAME = 'payment_method_nonce';
const FORM_ID = 'payment-form';

interface braintreeSetupSettings {
    onReady: any,
    onPaymentMethodReceived: any,
    onError: any,
    [key: string]: any
}

export default class CreditCard extends Component {
    form: HTMLFormElement;
    paymentMethodSelectors: HTMLInputElement[];
    errorContainers: HTMLElement[];
    braintreeContainer: HTMLElement;
    braintreeClientToken: string;
    nonceInputSelector: HTMLInputElement;
    braintreeSetupSettings: braintreeSetupSettings;
    scriptLoader: ScriptLoader;

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.querySelector(`#${FORM_ID}`);
        this.paymentMethodSelectors = <HTMLInputElement[]>Array.from(this.form.querySelectorAll('input[id^="paymentForm_paymentSelection"][type="radio"]'));
        this.errorContainers = <HTMLElement[]>Array.from(this.form.querySelectorAll('.braintree-error'));
        this.braintreeContainer = <HTMLElement>this.form.querySelector('.braintree-method');
        this.braintreeClientToken = this.braintreeContainer.getAttribute('data-braintree-client-token');
        this.nonceInputSelector = this.form.querySelector(`input[name="${NONCE_INPUT_NAME}"]`);
        this.scriptLoader = <ScriptLoader>this.querySelector('script-loader');

        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());

        this.paymentMethodSelectors.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                this.nonceInputSelector.value = '';
                this.emptyErrorContainers();
            });
        });
    }

    protected onScriptLoad(): void {
        if (!this.braintreeClientToken) {
            this.setupBraintree();
        }
    }

    protected getCurrentPaymentMethod(): any {
        this.paymentMethodSelectors.forEach((input: HTMLInputElement) => {
            if(input.checked) {
                return input.value;
            }
        });
    }

    protected getErrorTemplate(message: string = '') {
        return `<ul class="form-errors"><li>${message}</li></ul>`;
    }

    protected emptyErrorContainers(): void {
        this.errorContainers.forEach((container: HTMLElement) => {
            container.innerHTML = '';
        });
    }

    protected submitForm(nonce: string = '') {
        this.nonceInputSelector.value = nonce;
        this.form.submit();
    }

    protected errorHandler(error: any) {
        const braintreeCreditCardErrorSelector = this.form.querySelector('.braintree-credit-card-error');

        this.emptyErrorContainers();

        if (this.getCurrentPaymentMethod() === 'braintreeCreditCard') {
            return braintreeCreditCardErrorSelector.innerHTML = this.getErrorTemplate(error.message);
        }

        return this.submitForm();
    }

    protected paymentMethodHandler(response: any) {
        const paymentMethod = this.getCurrentPaymentMethod();
        const isWrongMethodSelected = (paymentMethod === 'braintreePayPal' && response.type !== 'PayPalAccount')
                                    || (paymentMethod === 'braintreeCreditCard' && response.type !== 'CreditCard');

        this.emptyErrorContainers();

        if (isWrongMethodSelected) {
            return this.errorHandler({
                message: 'Please choose a payment method'
            });
        }

        return this.submitForm(response.nonce);
    }

    protected readyHandler(): void {
        this.form.append(`<input type="hidden" name="${NONCE_INPUT_NAME}">`);
        const braintreeLoader = this.form.querySelector('.braintree-loader');
        const braintreeMethod = this.form.querySelector('.braintree-method');

        braintreeLoader.classList.remove('show')
        braintreeMethod.classList.add('show');
    }

    protected loadBraintree(): void {
        const braintreeCreditCardMethod = this.form.querySelector('.braintree-credit-card-method');

        this.braintreeSetupSettings = {
            onReady: this.readyHandler,
            onPaymentMethodReceived: this.paymentMethodHandler,
            onError: this.errorHandler
        };

        if (braintreeCreditCardMethod) {
            this.braintreeSetupSettings.id = FORM_ID;
            this.braintreeSetupSettings.hostedFields = {
                styles: {
                    'input': {
                        'font-size': '16px',
                        'color': '#333',
                        'font-family': 'Fira Sans, Arial, sans-serif'
                    }
                },
                number: {
                    selector: '#braintree-credit-card-number',
                    placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                    selector: '#braintree-credit-card-cvv',
                    placeholder: '123'
                },
                expirationDate: {
                    selector: '#braintree-credit-card-expiration-date',
                    placeholder: 'MM/YYYY'
                }
            };
        }
    }

    protected setupBraintree(): void {
        this.loadBraintree();
        braintree.setup(this.braintreeClientToken, 'custom', this.braintreeSetupSettings);
    }
}
