import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import braintree from 'braintree-web';
import paypal from 'paypal-checkout';

export default class BraintreePayPal extends BraintreePaymentForm {
    protected form: HTMLFormElement;

    protected readonly paymentMethodName: string = 'braintreePayPal';
    protected readonly paymentMethodTypeName: string = 'PayPalAccount';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);

        console.log(this.getAttribute('data-braintree-locale'));

        var self = this;

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

                            xhr.open('POST', '/paypal-express/payment/success', true);
                            xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
                            xhr.onreadystatechange = () => {
                                if (xhr.readyState === this.stateStatus.done && xhr.status === this.xhrStatuses.success) {
                                    const response = JSON.parse(xhr.responseText);

                                    window.location.href = response.redirectUrl;
                                }
                            };
                            xhr.send(userData);
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
}
