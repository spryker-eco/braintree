import './braintree-credit-card.scss';
import register from 'ShopUi/app/registry';
export default register('braintree-credit-card', () => import(/* webpackMode: "lazy" */'./braintree-credit-card'));

