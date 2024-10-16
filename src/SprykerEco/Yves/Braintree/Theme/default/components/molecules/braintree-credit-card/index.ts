import './braintree-credit-card.scss';
import register from 'ShopUi/app/registry';
export default register('braintree-credit-card', () => import(
    /* webpackMode: "lazy" */
    /* webpackChunkName: "braintree-credit-card" */
    './braintree-credit-card'));
