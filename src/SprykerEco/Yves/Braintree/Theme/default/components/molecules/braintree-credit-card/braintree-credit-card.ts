import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';

export default class BraintreeCreditCard extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected braintreeCreditCardMethod: HTMLElement;

    protected readonly paymentMethodName: string = 'braintreeCreditCard';
    protected readonly paymentMethodTypeName: string = 'CreditCard';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreeCreditCardMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);

        super.readyCallback();
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
