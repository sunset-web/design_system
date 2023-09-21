import { defineStore } from "ui.vue3.pinia";

export const dropListHeaderStore = defineStore("dropListHeaderStore", {
   state: () => {
      return {
         units: [],
      };
   },
   actions: {
      // Проверка наличия элемента
      getUnit(id) {
         return this.units.find((unit) => unit.id === id);
      },
      constructUnit(id, list) {
         // Получение значений
         let arrInStore = this.getUnit(id);

         if (arrInStore) {
            arrInStore.list = list;
         } else {
            this.units.push({
               id: id,
               list: list,
            });
         }
      },
   },
});
