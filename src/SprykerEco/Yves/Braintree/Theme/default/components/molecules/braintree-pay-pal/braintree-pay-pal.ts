import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import braintree from 'braintree-web';
import paypal from 'paypal-checkout';

export default class BraintreePayPal extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected submitBtn: HTMLButtonElement;

    protected readonly formId: string = 'payment-form';
    protected readonly paypalButtonSelector: string = '#paypal-button';
    protected readonly paymentMethodName: string = 'braintreePayPal';
    protected readonly paymentMethodTypeName: string = 'PayPalAccount';
    protected readonly nonceInputName: string = 'payment_method_nonce';

    protected readyCallback(): void {}

    protected init(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.submitBtn = <HTMLButtonElement>this.form.querySelector(`button[type='submit']`);

        super.createTokenField();
        this.mapEvents();
        this.createBrainTreeClient();
    }

    protected createBrainTreeClient(): void {
        braintree.client.create({
            authorization: this.braintreeClientToken
        }, (clientError, clientInstance) => {

            if (clientError) {
                console.error('Error creating client:', clientError);

                return;
            }

            this.createPayPalCheckoutComponent(clientInstance);
        });
    }

    protected createPayPalCheckoutComponent(clientInstance: object): void {
        braintree.paypalCheckout.create({
            client: clientInstance
        }, (error, paypalCheckoutInstance) => {
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

                        this.submitBtn.disabled = true;
                        this.form.submit();
                    });
                },

                onCancel: data => {
                    /* tslint:disable: no-console */
                    console.log('checkout.js payment cancelled: ', data);
                    /* tslint:enable: no-console */
                },

                onError: checkoutError => {
                    console.error('checkout.js error', checkoutError);
                }
            }, this.paypalButtonSelector);
        });
    }

    protected mapEvents(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                if (method.value === this.paymentMethodName) {
                    this.disableDefaultSubmit();
                }
            });
        });
    }

    protected disableDefaultSubmit(): void {
        this.submitBtn.addEventListener('click', (event: Event) => {
            event.preventDefault();
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
