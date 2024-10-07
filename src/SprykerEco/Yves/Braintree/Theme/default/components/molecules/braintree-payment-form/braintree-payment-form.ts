import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

interface BraintreeConfig {
    nonce: string;
    type: string;
}

interface BraintreeErrorConfig {
    message: string;
}

interface BraintreeSetupSettings {
    onPaymentMethodReceived(response: BraintreeConfig): void;
    onError(error: BraintreeErrorConfig): void;
}

export default class BraintreePaymentForm extends Component {
    protected form: HTMLFormElement;
    protected paymentMethods: HTMLInputElement[];
    protected nonceInputSelector: HTMLInputElement;
    protected errorContainer: HTMLElement;
    protected braintreeSetupSettings: BraintreeSetupSettings;
    protected scriptLoader: ScriptLoader;
    protected currentPaymentMethodValue: string = '';

    protected readonly formId: string = 'payment-form';
    protected readonly nonceInputName: string = 'payment_method_nonce';
    protected readonly paymentSelection: string = 'paymentForm[paymentSelection]';
    protected readonly integrationType: string = 'custom';
    protected readonly paymentMethodName: string = '';
    protected readonly paymentMethodTypeName: string = '';

    protected readyCallback(): void {}

    protected init(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);
        this.errorContainer = <HTMLElement>this.getElementsByClassName(`${this.jsName}__error`)[0];
        this.scriptLoader = <ScriptLoader>this.getElementsByClassName(`${this.jsName}__script-loader`)[0];

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
        this.tokenValue = nonce;
        this.form.submit();
    }

    protected createTokenField(): void {
        const tokenInput = document.createElement('input');
        tokenInput.setAttribute('type', 'hidden');
        tokenInput.setAttribute('name', `${this.nonceInputName}`);
        this.form.appendChild(tokenInput);
    }

    protected clearTokenValue(): void {
        this.tokenValue = '';
    }

    protected errorHandler(error: BraintreeErrorConfig): void {
        this.removeErrorMessage();

        if (this.currentPaymentMethodValue === this.paymentMethodName) {
            this.addErrorMessage(error.message);

            return;
        }

        this.submitForm();
    }

    protected paymentMethodHandler(response: BraintreeConfig): void {
        const isCurrentPaymentMethodEmpty = this.currentPaymentMethodValue === this.paymentMethodName;
        const isPaymentMethodSelected = response.type !== this.paymentMethodTypeName;
        const isWrongMethodSelected = isCurrentPaymentMethodEmpty && isPaymentMethodSelected;

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
        this.errorContainer.innerHTML = message;
        this.showErrorMessage(this.errorContainer);
    }

    protected showErrorMessage(errorContainer: HTMLElement): void {
        this.errorContainer.classList.remove(this.braintreeErrorMessageToggleClass);
    }

    protected removeErrorMessage(): void {
        this.hideErrorMessage(this.errorContainer);
        this.errorContainer.innerHTML = '';
    }

    protected hideErrorMessage(errorContainer: HTMLElement): void {
        this.errorContainer.classList.add(this.braintreeErrorMessageToggleClass);
    }

    protected set tokenValue(nonce: string) {
        this.nonceInputSelector.value = nonce;
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
