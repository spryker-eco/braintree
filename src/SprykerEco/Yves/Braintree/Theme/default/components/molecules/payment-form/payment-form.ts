declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

interface braintreeSetupSettings {
    onReady: any,
    onPaymentMethodReceived: any,
    onError: any,
    [key: string]: any
}

export default class CreditCard extends Component {
    form: HTMLFormElement;
    paymentMethods: HTMLInputElement[];
    braintreeSetupSettings: braintreeSetupSettings;
    braintreeCreditCardMethod: HTMLElement;
    scriptLoader: ScriptLoader;
    currentPaymentMethodValue: string = '';

    readonly formId: string = 'payment-form';
    readonly nonceInputName: string = 'payment_method_nonce';
    readonly braintreeCreditCard: string = 'braintreeCreditCard';
    readonly creditCard: string = 'CreditCard';
    readonly paymentSelection: string = 'paymentForm_paymentSelection';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[id^='${this.paymentSelection}']`));
        this.braintreeCreditCardMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);
        this.scriptLoader = <ScriptLoader>this.querySelector('script-loader');

        this.currentPaymentMethod();
        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());

        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                this.emptyNonceInputSelector();
                this.setCurrentPaymentMethodValue(method);
            });
        });
    }

    protected onScriptLoad(): void {
        if (!!this.braintreeClientToken) {
            this.setupBraintree();
        }
    }

    protected submitForm(nonce: string = '') {
        const nonceInputSelector = <HTMLInputElement>this.querySelector(`input[name='${this.nonceInputName}']`);
        nonceInputSelector.value = nonce;

        this.form.submit();
    }

    protected errorHandler(error: any) {
        const paymentMethod = this.currentPaymentMethodValue;
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);

        this.emptyErrorContainer();

        if (paymentMethod === this.braintreeCreditCard) {
            return errorContainer.innerHTML = this.errorTemplate(error.message);
        }

        return this.submitForm();
    }

    protected paymentMethodHandler(response: any) {
        const paymentMethod = this.currentPaymentMethodValue;
        const isWrongMethodSelected = (paymentMethod === this.braintreeCreditCard && response.type !== this.creditCard);

        this.emptyErrorContainer();

        if (isWrongMethodSelected) {
            return this.errorHandler({
                message: 'User did not enter a payment method'
            });
        }

        return this.submitForm(response.nonce);
    }

    protected readyHandler(): void {
        this.innerHTML += `<input type="hidden" name="${this.nonceInputName}" value="">`;
    }

    protected loadBraintree(): void {
        this.braintreeSetupSettings = {
            onReady: this.readyHandler(),
            onPaymentMethodReceived: this.paymentMethodHandler.bind(this),
            onError: this.errorHandler.bind(this)
        };

        if (this.braintreeCreditCardMethod) {
            this.braintreeSetupSettings.id = this.formId;
            this.braintreeSetupSettings.hostedFields = {
                styles: {
                    'input': {
                        'font-size': '14px',
                        'color': '#333',
                        'font-family': 'Arial, sans-serif'
                    },
                    '::-webkit-input-placeholder': {
                        'color': '#bbb'
                    },
                    ':-moz-placeholder': {
                        'color': '#bbb'
                    },
                    '::-moz-placeholder': {
                        'color': '#bbb'
                    },
                    ':-ms-input-placeholder': {
                        'color': '#bbb'
                    }
                },
                number: {
                    selector: `.${this.jsName}__number`,
                    placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                    selector: `.${this.jsName}__cvv`,
                    placeholder: '123'
                },
                expirationDate: {
                    selector: `.${this.jsName}__expiration-date`,
                    placeholder: 'MM/YYYY'
                }
            };
        }
    }

    protected setupBraintree(): void {
        this.loadBraintree();
        braintree.setup(this.braintreeClientToken, 'custom', this.braintreeSetupSettings);
    }

    currentPaymentMethod() {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            this.setCurrentPaymentMethodValue(method);
        });
    }

    setCurrentPaymentMethodValue(method: HTMLInputElement) {
        if(method.checked) {
            this.currentPaymentMethodValue = method.value;
        }
    }

    errorTemplate(message: string = '') {
        return `<ul class="list list--bullet list--alert"><li class="list__item">${message}</li></ul>`;
    }

    emptyNonceInputSelector() {
        const nonceInputSelector = <HTMLInputElement>this.querySelector(`input[name='${this.nonceInputName}']`);
        nonceInputSelector.value = '';
    }

    emptyErrorContainer() {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        errorContainer.innerHTML = '';
    }

    get braintreeClientToken(): string {
        return this.getAttribute('data-braintree-client-token');
    }
}
