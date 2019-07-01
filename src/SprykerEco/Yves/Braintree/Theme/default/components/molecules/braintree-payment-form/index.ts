import register from 'ShopUi/app/registry';
export default register('braintree-payment-form', () => import(/* webpackMode: "lazy" */'./braintree-payment-form'));
