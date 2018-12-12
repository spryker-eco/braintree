import PaymentForm from '../payment-form/payment-form';

export default class CreditCard extends PaymentForm {
    form: HTMLFormElement;
    braintreeCreditCardMethod: HTMLElement;

    readonly braintreeCreditCard: string = 'braintreeCreditCard';
    readonly creditCard: string = 'CreditCard';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreeCreditCardMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);

        super.readyCallback();
    }

    protected errorHandler(error: any) {
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);
        const paymentMethod = this.currentPaymentMethodValue;

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

    protected loadBraintree(): void {
        super.loadBraintree();

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
}
