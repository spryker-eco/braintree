import BraintreePaymentForm from '../braintree-payment-form/braintree-payment-form';
import dropin from 'braintree-web-drop-in';

interface BraintreeBillingAddress {
    givenName: string;
    surname: string;
    phoneNumber: string;
    streetAddress: string;
    extendedAddress: string;
    locality: string;
    region: string;
    postalCode: string;
    countryCodeAlpha2: string;
}

export default class BraintreeCreditCard extends BraintreePaymentForm {
    protected form: HTMLFormElement;
    protected braintreeCreditCardMethod: HTMLElement;
    protected paymentMethods: HTMLInputElement[];
    protected submitBtn: HTMLElement;

    /* tslint:disable: no-any */
    protected dropinInstance: any;

    /* tslint:enable: no-any */
    protected readonly dropInContainer: string = '#dropin_credit_card';
    protected readonly paymentMethodName: string = 'braintreeCreditCard';
    protected readonly paymentMethodTypeName: string = 'CreditCard';
    protected readonly nonceInputName: string = 'payment_method_nonce';

    protected readyCallback(): void {}

    protected init(): void {
        this.form = <HTMLFormElement>document.getElementById(`${this.formId}`);
        this.paymentMethods = <HTMLInputElement[]>Array.from(this.form.querySelectorAll(`input[name='${this.paymentSelection}']`));
        this.braintreeCreditCardMethod = <HTMLElement>this.form.getElementsByClassName(`${this.jsName}__method`)[0];
        this.submitBtn = <HTMLElement>this.form.querySelector(`button[type='submit']`);

        this.createDropinInstance();
        this.mapEvents();
    }

    protected mapEvents(): void {
        this.paymentMethods.forEach((method: HTMLInputElement) => {
            method.addEventListener('change', () => {
                if (method.value === this.paymentMethodName) {
                    this.switchSubmitButton();
                }
            });
        });
    }

    protected createDropinInstance(): void {
        dropin.create({
            authorization: this.braintreeClientToken,
            container: this.dropInContainer,
            threeDSecure: !!this.braintreeIs3dSecure,
        }, (createErr, instance) => {
            if (createErr) {
                /* tslint:disable: no-console */
                console.log(createErr);

                /* tslint:enable: no-console */
            }

            this.dropinInstance = instance;
        });
    }

    protected switchSubmitButton(): void {
        const nonceInputSelector = <HTMLInputElement>document.querySelector(`input[name='${this.nonceInputName}']`);

        this.submitBtn.addEventListener('click', e => {
            e.preventDefault();

            this.dropinInstance.requestPaymentMethod({
                threeDSecure: {
                    amount: this.braintreeAmount,
                    email: this.braintreeEmail,
                    billingAddress: this.braintreeBillingAddress
                }
            }, (err, payload) => {
                if (err) {
                    /* tslint:disable: no-console */
                    console.log('tokenization error:', err);

                    /* tslint:enable: no-console */
                    this.dropinInstance.clearSelectedPaymentMethod();

                    return;
                }

                if (this.braintreeIs3dSecure && !payload.liabilityShifted) {
                    /* tslint:disable: no-console */
                    console.log('Liability did not shift', payload);

                    /* tslint:enable: no-console */

                    return;
                }

                nonceInputSelector.value = payload.nonce;
                this.submitBtn.setAttribute('disabled', 'disabled');
                this.form.submit();
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

    protected get braintreeBillingAddress(): BraintreeBillingAddress {
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
        };
    }
}
