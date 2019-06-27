import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';

export default class BraintreePayPal extends BraintreePaymentForm {
    form: HTMLFormElement;
    braintreePayPalMethod: HTMLElement;

    readonly paymentMethodName: string = 'braintreePayPal';
    readonly paymentMethodTypeName: string = 'PayPalAccount';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreePayPalMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);

        super.readyCallback();
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
