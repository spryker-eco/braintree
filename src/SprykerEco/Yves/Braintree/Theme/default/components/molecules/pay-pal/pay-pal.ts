import PaymentForm from '../payment-form/payment-form';

export default class PayPal extends PaymentForm {
    form: HTMLFormElement;
    braintreePayPalMethod: HTMLElement;

    readonly braintreePayPal: string = 'braintreePayPal';
    readonly PayPalAccount: string = 'PayPalAccount';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.braintreePayPalMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);

        super.readyCallback();
    }

    protected errorHandler(error: any) {
        const paymentMethod = this.currentPaymentMethodValue;
        const errorContainer = <HTMLElement>this.querySelector(`.${this.jsName}__error`);

        this.emptyErrorContainer();

        if (paymentMethod === this.braintreePayPal) {
            return errorContainer.innerHTML = this.errorTemplate(error.message);
        }

        return this.submitForm();
    }

    protected paymentMethodHandler(response: any) {
        const paymentMethod = this.currentPaymentMethodValue;
        const isWrongMethodSelected = (paymentMethod === this.braintreePayPal && response.type !== this.PayPalAccount);

        this.emptyErrorContainer();

        if (isWrongMethodSelected) {
            return this.errorHandler({
                message: 'User did not enter a payment method'
            });
        }

        return this.submitForm(response.nonce);
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
