declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

interface braintreeSetupSettings {
    onReady: any,
    onPaymentMethodReceived: any,
    onError: any,
    [key: string]: any
}

export default class PaymentForm extends Component {
    form: HTMLFormElement;
    paymentMethods: HTMLInputElement[];
    braintreeSetupSettings: braintreeSetupSettings;
    scriptLoader: ScriptLoader;
    currentPaymentMethodValue: string = '';

    readonly formId: string = 'payment-form';
    readonly nonceInputName: string = 'payment_method_nonce';
    readonly paymentSelection: string = 'paymentForm_paymentSelection';

    constructor() {
        super();
    }

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[id^='${this.paymentSelection}']`));
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
        return this.submitForm();
    }

    protected paymentMethodHandler(response: any) {
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
