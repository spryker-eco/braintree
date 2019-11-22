import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import braintree from 'braintree-web';
import dropin from 'braintree-web-drop-in';

export default class BraintreePayPal extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected braintreePayPalMethod: HTMLElement;

    protected readonly dropInContainer: string = '#dropin_paypal';
    protected readonly paymentMethodName: string = 'braintreePayPal';
    protected readonly paymentMethodTypeName: string = 'PayPalAccount';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreePayPalMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);
    }

    protected loadBraintree(): void {
        super.loadBraintree();

        if (this.braintreePayPalMethod) {
            this.braintreeSetupSettings.paypal = {
                container: `${this.jsName}__container`
            };
        }
    }
}
