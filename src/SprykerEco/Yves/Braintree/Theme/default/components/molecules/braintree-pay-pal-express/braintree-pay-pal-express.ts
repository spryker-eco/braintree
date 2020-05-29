import Component from 'ShopUi/models/component';
import braintree from 'braintree-web';
import client from 'braintree-web/client';
import paypalCheckout from 'braintree-web/paypal-checkout';
import paypal from 'paypal-checkout';

interface BraintreeData {
    env: string;
    token: string;
    currency: string;
    amount: number;
    successUrl: string;
}

interface PaypalCheckoutInstance {
    createPayment: Function;
    tokenizePayment: Function;
}

interface ClientInstance {
    getConfiguration: Function;
    request: Function;
    configuration: object;
}

interface ClientError {
    name: string;
    code: string;
    message: string;
    type: string;
    details: object;
}

interface Statuses {
    done?: number;
    success?: number;
}

export default class BraintreePayPalExpress extends Component {
    protected braintreeData: BraintreeData;
    protected stateStatus: Statuses = {
        done: 4
    };
    protected xhrStatuses: Statuses = {
        success: 200
    };

    protected readyCallback(): void {}

    protected init(): void {
        this.braintreeData = this.parseBraintreeData();
        this.registerBraintreeClient();
    }

    protected registerBraintreeClient(): void {
        client.create({
            authorization: this.braintreeData.token
        }, this.registerBraintreeClientCallback.bind(this));
    }

    protected registerBraintreeClientCallback(clientError: ClientError, clientInstance: ClientInstance): void {
        if (clientError) {
            console.error('PayPal checkout register error!', clientError);

            return;
        }

        try {
            this.initPaypalCheckout(clientInstance);
        } catch (error) {
            console.error('Init PayPal checkout error!', error);
        }
    }

    protected async initPaypalCheckout(clientInstance: ClientInstance): Promise<void> {
        const checkoutInstance = <PaypalCheckoutInstance>await paypalCheckout.create({
            client: clientInstance
        });

        paypal.Button.render({
            env: this.braintreeData.env,
            commit: false,
            style: {
                size: 'responsive'
            },
            payment: () => this.onPaymentHandler(checkoutInstance),
            onAuthorize: (data, actions) => this.onAuthorizeHandler(data, actions, checkoutInstance),
            onError: error => {
                console.error('PayPal checkout button render error!', error);
            }
        }, `.${ this.name }`);
    }

    protected onPaymentHandler(checkoutInstance: PaypalCheckoutInstance): Function {
        return checkoutInstance.createPayment({
            flow: 'checkout',
            intent: 'authorize',
            amount: this.braintreeData.amount,
            currency: this.braintreeData.currency,
            enableShippingAddress: true,
            shippingAddressEditable: true
        });
    }

    protected onAuthorizeHandler(data: object, actions: object, checkoutInstance: PaypalCheckoutInstance): Function {
        return checkoutInstance.tokenizePayment(data).then(payload => {
            payload.amount = this.braintreeData.amount;
            payload.currency = this.braintreeData.currency;

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
    }

    protected parseBraintreeData(): BraintreeData {
        const braintreeData = JSON.parse(this.dataset.braintree);
        this.dataset.braintree = '';

        return braintreeData;
    }
}
