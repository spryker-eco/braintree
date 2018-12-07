declare var braintree: any;

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

export default class CreditCard extends Component {
    scriptLoader: ScriptLoader;

    protected readyCallback(): void {
        this.scriptLoader = <ScriptLoader>this.querySelector(`.${this.jsName}__script-loader`);

        this.mapEvents();
    }

    protected mapEvents(): void {

    }
}
