import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import dropin from 'braintree-web-drop-in';

export default class BraintreeCreditCard extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected braintreeCreditCardMethod: HTMLElement;

    protected readonly paymentMethodName: string = 'braintreeCreditCard';
    protected readonly paymentMethodTypeName: string = 'CreditCard';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreeCreditCardMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);

        // console.log(this.braintreeEmail());
        // console.log(this.braintreeBillingAddress());
    }

    protected loadBraintree(): void {
        super.loadBraintree();

        console.log(this.braintreeEmail());
        console.log(this.braintreeBillingAddress());

        if (this.braintreeCreditCardMethod) {
            // this.braintreeSetupSettings.id = this.formId;
            // this.braintreeSetupSettings.hostedFields = {
            //     styles: {
            //         'input': {
            //             'font-size': '14px',
            //             'color': '#333',
            //             'font-family': 'Arial, sans-serif'
            //         },
            //         '::-webkit-input-placeholder': {
            //             'color': '#bbb'
            //         },
            //         ':-moz-placeholder': {
            //             'color': '#bbb'
            //         },
            //         '::-moz-placeholder': {
            //             'color': '#bbb'
            //         },
            //         ':-ms-input-placeholder': {
            //             'color': '#bbb'
            //         }
            //     },
            //     number: {
            //         selector: `.${this.jsName}__number`,
            //         placeholder: '4111 1111 1111 1111'
            //     },
            //     cvv: {
            //         selector: `.${this.jsName}__cvv`,
            //         placeholder: '123'
            //     },
            //     expirationDate: {
            //         selector: `.${this.jsName}__expiration-date`,
            //         placeholder: 'MM/YYYY'
            //     }
            // };
        }
    }

    protected braintreeEmail(): string {
        return this.getAttribute('data-braintree-email');
    }

    protected braintreeBillingAddress(): any {
        return {
            givenName: this.getAttribute('data-braintree-billing-address-given-name'),
            surname: this.getAttribute('data-braintree-billing-address-surname'),
            phoneNumber: this.getAttribute('data-braintree-billing-address-phoneNumber'),
            streetAddress: this.getAttribute('data-braintree-billing-address-streetAddress'),
            extendedAddress: this.getAttribute('data-braintree-billing-address-extendedAddress'),
            locality: this.getAttribute('data-braintree-billing-address-locality'),
            region: this.getAttribute('data-braintree-billing-address-region'),
            postalCode: this.getAttribute('data-braintree-billing-address-postalCode'),
            countryCodeAlpha2: this.getAttribute('data-braintree-billing-address-countryCodeAlpha2')
        }
    }
}
