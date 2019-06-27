import register from 'ShopUi/app/registry';
export default register('braintree-pay-pal-express', () => import(/* webpackMode: "lazy" */'./braintree-pay-pal-express'));
