export const Item = {
   props: ["item"],
   computed: {
      colorView() {
         return this.item.UF_VIEW == 'false' ? false : true;
      }
   },
   methods: {
      // Отправка события и изменение флага просмотра
      updateView() {
         if (!this.colorView) {
            this.$Bitrix.eventEmitter.emitAsync('updateview', { id: this.item.ID }).then((result) => {
               if (result[0]) {
                  this.item.UF_VIEW = 'true';
               }
            });
         }
      }
   },
   /* html */
   template: `
   <li ref="elem" :class="{checked: !colorView}" @mouseover="updateView">
      <div class="date">{{item.UF_DATE}}</div>
      <div>{{item.UF_TEXT}}</div>
   </li>
   `,
};
