declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

interface braintreeConfig {
    nonce: string,
    type: string
}

interface braintreeErrorConfig {
    message: string
}

export default class PaymentForm extends Component {
    form: HTMLFormElement;
    paymentMethods: HTMLInputElement[];
    nonceInputSelector: HTMLInputElement;
    braintreeSetupSettings: any;
    scriptLoader: ScriptLoader;
    currentPaymentMethodValue: string = '';

    readonly formId: string = 'payment-form';
    readonly nonceInputName: string = 'payment_method_nonce';
    readonly paymentSelection: string = 'paymentForm[paymentSelection]';
    readonly integrationType: string = 'custom';
    readonly paymentMethodName: string = '';
    readonly paymentMethodTypeName: string = '';

    constructor() {
        super();
    }

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.nonceInputSelector = <HTMLInputElement>this.querySelector(`input[name='${this.nonceInputName}']`);
        this.scriptLoader = <ScriptLoader>this.querySelector('script-loader');

        this.setCurrentPaymentMethod();
        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());

        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                this.clearNonceInputSelector();
                this.setCurrentPaymentMethodValue(method);
            });
        });
    }

    protected onScriptLoad(): void {
        if (!!this.braintreeClientToken) {
            this.setupBraintree();
        }
    }

    protected submitForm(nonce: string = ''): void {
        this.nonceInputSelector.value = nonce;

        this.form.submit();
    }

    protected errorHandler(error: braintreeErrorConfig): any {
        const paymentMethod = this.currentPaymentMethodValue;

        this.removeErrorMessage();

        if (paymentMethod === this.paymentMethodName) {
            return this.addErrorMessage(error.message);
        }

        return this.submitForm();
    }

    protected paymentMethodHandler(response: braintreeConfig): void {
        const paymentMethod = this.currentPaymentMethodValue;
        const isWrongMethodSelected = (paymentMethod === this.paymentMethodName && response.type !== this.paymentMethodTypeName);

        this.removeErrorMessage();

        if (isWrongMethodSelected) {
            return this.errorHandler({
                message: this.braintreeErrorMessage
            });
        }

        return this.submitForm(response.nonce);
    }

    protected loadBraintree(): void {
        this.braintreeSetupSettings = {
            onPaymentMethodReceived: this.paymentMethodHandler.bind(this),
            onError: this.errorHandler.bind(this)
        };
    }

    protected setupBraintree(): void {
        this.loadBraintree();
        braintree.setup(this.braintreeClientToken, this.integrationType, this.braintreeSetupSettings);
    }

    setCurrentPaymentMethod(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            this.setCurrentPaymentMethodValue(method);
        });
    }

    setCurrentPaymentMethodValue(method: HTMLInputElement): void {
        if(method.checked) {
            this.currentPaymentMethodValue = method.value;
        }
    }

    addErrorMessage(message: string = ''): string {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        this.showErrorMessage(errorContainer);

        return errorContainer.innerHTML = message;
    }

    showErrorMessage(errorContainer: HTMLElement): void {
        errorContainer.classList.remove('is-hidden');
    }

    clearNonceInputSelector(): void {
        this.nonceInputSelector.value = '';
    }

    removeErrorMessage(): void {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        this.hideErrorMessage(errorContainer);
        errorContainer.innerHTML = '';
    }

    hideErrorMessage(errorContainer: HTMLElement): void {
        errorContainer.classList.add('is-hidden');
    }

    get braintreeClientToken(): string {
        return this.getAttribute('data-braintree-client-token');
    }

    get braintreeErrorMessage(): string {
        return this.getAttribute('data-error-message');
    }
}
