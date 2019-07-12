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

export default class BraintreePaymentForm extends Component {
    protected form: HTMLFormElement;
    protected paymentMethods: HTMLInputElement[];
    protected nonceInputSelector: HTMLInputElement;
    protected braintreeSetupSettings: any;
    protected scriptLoader: ScriptLoader;
    protected currentPaymentMethodValue: string = '';

    protected readonly formId: string = 'payment-form';
    protected readonly nonceInputName: string = 'payment_method_nonce';
    protected readonly paymentSelection: string = 'paymentForm[paymentSelection]';
    protected readonly integrationType: string = 'custom';
    protected readonly paymentMethodName: string = '';
    protected readonly paymentMethodTypeName: string = '';

    constructor() {
        super();
    }

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);
        this.scriptLoader = <ScriptLoader>this.querySelector('script-loader');

        this.setCurrentPaymentMethod();
        if (!this.nonceInputSelector) {
            this.createTokenField();
        }
        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', () => this.onScriptLoad());

        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                this.clearTokenValue();
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
        this.initTokenValue(nonce);
        this.form.submit();
    }

    protected createTokenField(): void {
        const tokenInput = document.createElement('input');
        tokenInput.setAttribute('type', 'hidden');
        tokenInput.setAttribute('name', `${this.nonceInputName}`);
        this.form.appendChild(tokenInput);
    }

    protected initTokenValue(nonce: string): void {
        const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);
        nonceInputSelector.value = nonce;
    }

    protected clearTokenValue(): void {
        const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);
        nonceInputSelector.value = '';
    }

    protected errorHandler(error: braintreeErrorConfig): void {
        const paymentMethod = this.currentPaymentMethodValue;

        this.removeErrorMessage();

        if (paymentMethod === this.paymentMethodName) {
            this.addErrorMessage(error.message);

            return;
        }

        this.submitForm();
    }

    protected paymentMethodHandler(response: braintreeConfig): void {
        const paymentMethod = this.currentPaymentMethodValue;
        const isWrongMethodSelected = (paymentMethod === this.paymentMethodName && response.type !== this.paymentMethodTypeName);

        this.removeErrorMessage();

        if (isWrongMethodSelected) {
            this.errorHandler({
                message: this.braintreeErrorMessage
            });

            return;
        }

        this.submitForm(response.nonce);
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

    protected setCurrentPaymentMethod(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            this.setCurrentPaymentMethodValue(method);
        });
    }

    protected setCurrentPaymentMethodValue(method: HTMLInputElement): void {
        if (method.checked) {
            this.currentPaymentMethodValue = method.value;
        }
    }

    protected addErrorMessage(message: string = ''): void {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        this.showErrorMessage(errorContainer);

        errorContainer.innerHTML = message;
    }

    protected showErrorMessage(errorContainer: HTMLElement): void {
        errorContainer.classList.remove(this.braintreeErrorMessageToggleClass);
    }

    protected removeErrorMessage(): void {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        this.hideErrorMessage(errorContainer);
        errorContainer.innerHTML = '';
    }

    protected hideErrorMessage(errorContainer: HTMLElement): void {
        errorContainer.classList.add(this.braintreeErrorMessageToggleClass);
    }

    protected get braintreeErrorMessageToggleClass(): string {
        return this.getAttribute('data-error-message-toggle-class');
    }

    protected get braintreeClientToken(): string {
        return this.getAttribute('data-braintree-client-token');
    }

    protected get braintreeErrorMessage(): string {
        return this.getAttribute('data-error-message');
    }
}
