import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import braintree from 'braintree-web';
import paypal from 'paypal-checkout';

export default class BraintreePayPal extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected submitBtn: HTMLElement;

    protected readonly formId: string = 'payment-form';
    protected readonly paymentMethodName: string = 'braintreePayPal';
    protected readonly paymentMethodTypeName: string = 'PayPalAccount';
    protected readonly nonceInputName: string = 'payment_method_nonce';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.submitBtn = <HTMLElement>this.form.querySelector(`button[type='submit']`);

        const tokenInput = document.createElement('input');
        tokenInput.setAttribute('type', 'hidden');
        tokenInput.setAttribute('name', `${this.nonceInputName}`);
        this.form.appendChild(tokenInput);

        var self = this;

        this.mapEvents();

        // // Create a client.
        braintree.client.create({
            authorization: this.braintreeClientToken
        }, function (clientErr, clientInstance) {

            // Stop if there was a problem creating the client.
            // This could happen if there is a network error or if the authorization
            // is invalid.
            if (clientErr) {
                console.error('Error creating client:', clientErr);
                return;
            }

            // Create a PayPal Checkout component.
            braintree.paypalCheckout.create({
                client: clientInstance
            }, function (err, paypalCheckoutInstance) {

                // Set up PayPal with the checkout.js library
                paypal.Button.render({
                    env: self.getAttribute('data-braintree-env'),
                    locale: self.getAttribute('data-braintree-locale'),
                    commit: true,

                    payment: function() {
                        return paypalCheckoutInstance.createPayment({
                            flow: 'checkout',
                            intent: 'authorize',
                            amount: self.getAttribute('data-braintree-amount'),
                            currency: self.getAttribute('data-braintree-currency'),
                            enableShippingAddress: true,
                            shippingAddressEditable: true
                        });
                    },

                    onAuthorize: function (data, actions) {
                        return paypalCheckoutInstance.tokenizePayment(data).then((payload) => {
                            payload['amount'] = self.getAttribute('data-braintree-amount');
                            payload['currency'] = self.getAttribute('data-braintree-currency');

                            const xhr = new XMLHttpRequest();
                            const userData = JSON.stringify(payload);

                            const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${self.nonceInputName}']`);
                            nonceInputSelector.value = payload.nonce;
                            self.submitBtn.setAttribute('disabled', 'disabled');
                            self.form.submit();
                        });
                    },

                    onCancel: function (data) {
                        console.log('checkout.js payment cancelled');
                        console.log(data);
                    },

                    onError: function (err) {
                        console.error('checkout.js error', err);
                    }
                }, '#paypal-button');

            });
        });
    }

    protected mapEvents(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                if (method.value == this.paymentMethodName) {
                    this.switchSubmitButton();
                }
            });
        });
    }

    protected switchSubmitButton(): void {
        this.submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
        });
    }
}
