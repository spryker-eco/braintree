declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

export default class CreditCard extends Component {
    form: HTMLFormElement;
    paymentMethodSelectors: HTMLInputElement[];
    errorContainers: HTMLElement[];
    braintreeContainer: HTMLElement;
    braintreeClientToken: string;
    nonceInputSelector: HTMLInputElement;

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.querySelector('#payment-form');
        this.paymentMethodSelectors = <HTMLInputElement[]>Array.from(this.form.querySelectorAll('input[id^="paymentForm_paymentSelection"][type="radio"]'));
        this.errorContainers = <HTMLElement[]>Array.from(this.form.querySelectorAll('.braintree-error'));
        this.braintreeContainer = <HTMLElement>this.form.querySelector('.braintree-method');
        this.braintreeClientToken = this.braintreeContainer.getAttribute('data-braintree-client-token').length ? this.braintreeContainer.getAttribute('data-braintree-client-token') : null;
        this.nonceInputSelector = this.form.querySelector('input[name="payment_method_nonce"]');
        this.mapEvents();
    }

    protected mapEvents(): void {
        console.log(this.getErrorTemplate('privet'));
    }

    protected getCurrentPaymentMethod(): any {
        this.paymentMethodSelectors.forEach((input: HTMLInputElement) => {
            if(input.checked) {
                return input.value;
            }
        });
    }

    protected getErrorTemplate(message: String): string {
        return `<ul class="form-errors"><li>${message}</li></ul>`;
    }

    protected submitForm(nonce = ''): any {
        this.nonceInputSelector.value = nonce || '';
        this.form.submit();
    }

    protected errorHandler(error): string {
        const braintreeCreditCardErrorSelector = this.form.querySelector('.braintree-credit-card-error');

        this.errorContainers.forEach((container) => {
            container.innerHTML = '';
        });

        if (this.getCurrentPaymentMethod() === 'braintreeCreditCard') {
            return braintreeCreditCardErrorSelector.innerHTML = this.getErrorTemplate(error.message);
        }

        return this.submitForm();
    }
}
