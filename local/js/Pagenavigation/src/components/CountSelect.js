export const CountSelect = {
   props: ["count", "countDef", "current"],
   data() {
      return {
         eachArr: [1, 2, 3],
         countActive: this.count,
      }
   },
   computed: {
      pageCount() {
         return this.countDef == this.count ? this.count : this.countDef;
      }
   },
   methods: {
      // Переключение кол-ва
      clickCounter(e) {
         this.countActive = e.target.dataset.count;

         let urlParams = new URL(window.location.href)
         urlParams.searchParams.set('count', this.countActive);
         history.pushState(null, null, urlParams);

         this.$Bitrix.eventEmitter.emit('countpage', { pageN: this.current, count: this.countActive });
      },
   },
   /* html */
   template: `
   <div class="show flex ai-center">
      <p>{{$Bitrix.Loc.getMessage('COUNT_SELECT-TITLE')}}</p>
      <div class="__select __select-small __select-count-js" data-state="">
         <div class="__select__title __select__title-small __select-count-title-js" data-default="Option 0">{{pageCount}}</div>
         <div class="__select__content __select__content-small">
            <template v-for="(item, index) in eachArr" :item="item" :key="index">
               <input :id="'singleSelect-n'+item" class="__select__input" type="radio" name="singleSelect" />
               <label :for="'singleSelect-n'+item"
                  class="__select__label __select__label-small __select-count-label-js" @click="clickCounter" :data-count="count*item">{{count*item}}</label>
            </template>

         </div>
      </div>
   </div>
   `,
};
