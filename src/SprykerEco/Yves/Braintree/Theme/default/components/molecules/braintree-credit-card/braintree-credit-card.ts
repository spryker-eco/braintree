var dropinInstance;

import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import dropin from 'braintree-web-drop-in';

export default class BraintreeCreditCard extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected braintreeCreditCardMethod: HTMLElement;
    protected paymentMethods: HTMLInputElement[];
    protected submitBtn: HTMLElement;

    protected readonly dropInContainer: string = '#dropin_credit_card';
    protected readonly paymentMethodName: string = 'braintreeCreditCard';
    protected readonly paymentMethodTypeName: string = 'CreditCard';
    protected readonly nonceInputName: string = 'payment_method_nonce';

    protected readyCallback(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.braintreeCreditCardMethod = <HTMLElement>this.form.querySelector(`.${this.jsName}__method`);
        this.submitBtn = <HTMLElement>this.form.querySelector(`button[type='submit']`);

        dropin.create({
            authorization: this.braintreeClientToken,
            container: this.dropInContainer,
            threeDSecure: !!this.braintreeIs3dSecure,
        }, function (createErr, instance) {
            if (createErr) {
                console.log(createErr);
            }

            dropinInstance = instance;
        });

        this.mapEvents();
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
        var self = this;

        this.submitBtn.addEventListener('click', (e) => {
            e.preventDefault();

            dropinInstance.requestPaymentMethod({
                threeDSecure: {
                    amount: this.braintreeAmount,
                    email: this.braintreeEmail,
                    billingAddress: this.braintreeBillingAddress
                }
            }, function(err, payload) {
                if (err) {
                    console.log('tokenization error:');
                    console.log(err);
                    dropinInstance.clearSelectedPaymentMethod();

                    return;
                }

                if (self.braintreeIs3dSecure && !payload.liabilityShifted) {
                    console.log('Liability did not shift', payload);
                    return;
                }

                const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${self.nonceInputName}']`);
                nonceInputSelector.value = payload.nonce;
                self.submitBtn.setAttribute('disabled', 'disabled');
                self.form.submit();
            });
        });
    }

    protected get braintreeIs3dSecure(): string {
        return this.getAttribute('data-braintree-is-3d-secure');
    }

    protected get braintreeAmount(): string {
        return this.getAttribute('data-braintree-amount');
    }

    protected get braintreeEmail(): string {
        return this.getAttribute('data-braintree-email');
    }

    protected get braintreeBillingAddress(): any {
        return {
            givenName: this.getAttribute('data-braintree-billing-address-given-name'),
            surname: this.getAttribute('data-braintree-billing-address-surname'),
            phoneNumber: this.getAttribute('data-braintree-billing-address-phoneNumber'),
            streetAddress: this.getAttribute('data-braintree-billing-address-streetAddress'),
            extendedAddress: this.getAttribute('data-braintree-billing-address-extendedAddress'),
            locality: this.getAttribute('data-braintree-billing-address-locality'),
            region: this.getAttribute('data-braintree-billing-address-region'),
            postalCode: this.getAttribute('data-braintree-billing-address-postalCode'),
            countryCodeAlpha2: this.getAttribute('data-braintree-billing-address-countryCodeAlpha2')
        }
    }
}
