export const Item = {
   props: ["item"],
   /* html */
   template: `
   <li ref="elem" :data-id="item.id">
      <div class="date">{{item.UF_DATE}}</div>
      <div>{{item.UF_TEXT}}</div>
   </li>
   `,
};
