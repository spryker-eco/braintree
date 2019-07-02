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

interface IStatuses {
    [key: string]: number;
};

export default class BraintreePayPalExpress extends Component {
    protected braintreeData: IBraintreeData;
    protected stateStatus: IStatuses = {
        done: 4
    };
    protected xhrStatuses: IStatuses = {
        success: 200
    };

    protected readyCallback(): void {
        this.braintreeData = this.parseBraintreeData();
        this.registerClient();
    };

    protected registerClient(): void {
        client.create({
            authorization: this.braintreeData.token
        }, this.registerCallback.bind(this));
    };

    protected registerCallback(error, clientInstance): void {
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
            env: this.braintreeData.env,
            commit: false,
            style: {
                size: 'responsive'
            },
            payment: () => this.onPaymentHandler(paypalCheckoutInstance),
            onAuthorize: (data, actions) => this.onAuthorizeHandler(data, actions, paypalCheckoutInstance),
            onError: (error) => {
                console.error('PayPal checkout button render error!', error);
            }
        }, `.${ this.name }`);
    };

    protected onPaymentHandler(paypalCheckoutInstance: IPaypalCheckoutInstance): Function {
        return paypalCheckoutInstance.createPayment({
            flow: 'checkout',
            intent: 'authorize',
            amount: this.braintreeData.amount,
            currency: this.braintreeData.currency,
            enableShippingAddress: true,
            shippingAddressEditable: true
        });
    };

    protected onAuthorizeHandler(data, actions, paypalCheckoutInstance: IPaypalCheckoutInstance): Function {
        return paypalCheckoutInstance.tokenizePayment(data).then((payload) => {
            payload['amount'] = this.braintreeData.amount;
            payload['currency'] = this.braintreeData.currency;

            const xhr = new XMLHttpRequest();
            const userData = JSON.stringify(payload);

            xhr.open('POST', this.braintreeData.successUrl, true);
            xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
            xhr.onreadystatechange = () => {
                if (xhr.readyState === this.stateStatus.done && xhr.status === this.xhrStatuses.success) {
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
