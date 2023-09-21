import { defineStore } from "ui.vue3.pinia";

export const commentsStore = defineStore("commentsStore", {
  state: () => {
    return {
      units: [],
    };
  },
  actions: {
    getUnit(id) {
      let arrInStore = this.units.find((unit) => unit.id === id);
      return arrInStore;
    },
    constructUnit(id, comments) {
      let arrInStore = this.units.find((unit) => unit.id === id);
      if (arrInStore) {
        arrInStore.comments = comments;
      } else {
        this.units.push({
          id: id,
          comments: comments,
        });
      }
    },
    createComments(id, comment) {
      let arrInStore = this.units.find((unit) => unit.id === id);
      if (arrInStore) {
        arrInStore.comments.push(comment);
      } else {
        this.units.push({
          id: id,
          comments: comment,
        });
      }
      return comment.id;
    },
    loadComments(id, comments) {
      let arrInStore = this.units.find((unit) => unit.id === id);
      arrInStore.comments.unshift(...comments);
    },
  },
});
