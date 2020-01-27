import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import braintree from 'braintree-web';
import paypal from 'paypal-checkout';

export default class BraintreePayPal extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected submitBtn: HTMLElement;

    protected readonly formId: string = 'payment-form';
    protected readonly paypalButtonSelector: string = '#paypal-button';
    protected readonly paymentMethodName: string = 'braintreePayPal';
    protected readonly paymentMethodTypeName: string = 'PayPalAccount';
    protected readonly nonceInputName: string = 'payment_method_nonce';

    protected readyCallback(): void {}

    protected init(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.submitBtn = <HTMLElement>this.form.querySelector(`button[type='submit']`);

        super.createTokenField();
        this.mapEvents();
        this.createBrainTreeClient();
    }

    protected createBrainTreeClient(): void {
        braintree.client.create({
            authorization: this.braintreeClientToken
        }, (clientError, clientInstance) => {

            // stop if there was a problem creating the client.
            // this could happen if there is a network error or if the authorization
            // is invalid.
            if (clientError) {
                console.error('Error creating client:', clientError);

                return;
            }

            // create a PayPal Checkout component.
            braintree.paypalCheckout.create({
                client: clientInstance
            }, (err, paypalCheckoutInstance) => {

                // set up PayPal with the checkout.js library
                paypal.Button.render({
                    env: this.environment,
                    locale: this.locale,
                    commit: true,

                    payment: () => {
                        return paypalCheckoutInstance.createPayment({
                            flow: 'checkout',
                            intent: 'authorize',
                            amount: this.amount,
                            currency: this.currency,
                            enableShippingAddress: true,
                            shippingAddressEditable: true
                        });
                    },

                    onAuthorize: (data, actions) => {
                        const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);

                        return paypalCheckoutInstance.tokenizePayment(data).then(payload => {
                            const xhr = new XMLHttpRequest();
                            const userData = JSON.stringify(payload);

                            payload.amount = this.amount;
                            payload.currency = this.currency;
                            nonceInputSelector.value = payload.nonce;

                            this.submitBtn.setAttribute('disabled', 'disabled');
                            this.form.submit();
                        });
                    },

                    onCancel: data => {
                        /* tslint:disable: no-console */
                        console.log('checkout.js payment cancelled: ', data);

                        /* tslint:enable: no-console */
                    },

                    onError: error => {
                        console.error('checkout.js error', error);
                    }
                }, this.paypalButtonSelector);

            });
        });
    }

    protected mapEvents(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                if (method.value === this.paymentMethodName) {
                    this.switchSubmitButton();
                }
            });
        });
    }

    protected switchSubmitButton(): void {
        this.submitBtn.addEventListener('click', e => {
            e.preventDefault();
        });
    }

    protected get environment(): string {
        return this.getAttribute('data-braintree-env');
    }

    protected get locale(): string {
        return this.getAttribute('data-braintree-locale');
    }

    protected get amount(): string {
        return this.getAttribute('data-braintree-amount');
    }

    protected get currency(): string {
        return this.getAttribute('data-braintree-currency');
    }
}
