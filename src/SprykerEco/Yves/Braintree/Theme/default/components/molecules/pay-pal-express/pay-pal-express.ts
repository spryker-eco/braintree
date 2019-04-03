import Component from 'ShopUi/models/component';
import braintree from 'braintree-web';
import client from 'braintree-web/client';
import paypalCheckout from 'braintree-web/paypal-checkout';
import paypal from 'paypal-checkout';

interface IBraintreeData {
    env: string;
    token: string;
    currency: string;
    amount: number;
    successUrl: string;
};

interface IPaypalCheckoutInstance {
    createPayment: Function;
    tokenizePayment: Function;
};

export default class PayPalExpress extends Component {
    parsedBraintreeData: IBraintreeData;

    protected readyCallback(): void {
        this.parsedBraintreeData = <IBraintreeData>this.parseBraintreeData();
        this.registerClient();
    };

    protected registerClient(): void {
        client.create({
            authorization: this.parsedBraintreeData.token
        }, this.registerCallback.bind(this));
    };

    protected registerCallback(error, clientInstance) {
        if (error) {
            console.error('PayPal checkout register error!', error);
            return;
        }

        try {
            this.initPaypalCheckout(clientInstance);
        } catch (error) {
            console.error('Init PayPal checkout error!', error);
        }
    };

    protected async initPaypalCheckout(clientInstance): Promise<void> {
        const paypalCheckoutInstance = <IPaypalCheckoutInstance>await paypalCheckout.create({
            client: clientInstance
        });

        paypal.Button.render({
            env: this.parsedBraintreeData.env,
            commit: false,
            style: { size: 'responsive' },
            payment: () => this.onPaymentHandler(paypalCheckoutInstance),
            onAuthorize: (data, actions) => this.onAuthorizeHandler(data, actions, paypalCheckoutInstance),
            onError: (error) => {
                console.error('PayPal checkout button render error!', error);
            }
        }, `.${ this.name }`);
    };

    protected onPaymentHandler(paypalCheckoutInstance: IPaypalCheckoutInstance) {
        return paypalCheckoutInstance.createPayment({
            flow: 'checkout',
            intent: 'authorize',
            amount: this.parsedBraintreeData.amount,
            currency: this.parsedBraintreeData.currency,
            enableShippingAddress: true
        });
    };

    protected onAuthorizeHandler(data, actions, paypalCheckoutInstance: IPaypalCheckoutInstance) {
        return paypalCheckoutInstance.tokenizePayment(data).then((payload) => {
            const xhr = new XMLHttpRequest();
            const userData = JSON.stringify(payload);

            xhr.open('POST', this.parsedBraintreeData.successUrl, true);
            xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    window.location.href = response.redirectUrl;
                }
            };
            xhr.send(userData);
        });
    };

    protected parseBraintreeData(): IBraintreeData {
        const braintreeData = JSON.parse(this.dataset.braintree);
        this.dataset.braintree = '';

        return braintreeData;
    };
}
