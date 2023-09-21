export const CommentCard = {
  mounted() {
    this.$nextTick(function () {
      this.$refs.elem.scrollIntoView();
    });
  },
  props: ["comment"],
  computed: {
    htmlText() {
      // функция из index.js
      return findAndReplaceLink(this.comment.text);
    },
  },
  /* html */
  template: `
    <li ref="elem" class="comments__item" :data-id="comment.id">
      <div class="comments__date">{{comment.date}}</div>
      <div class="comments__message">{{$Bitrix.Loc.getMessage('COMMENT_USER', {'#USER#': comment.user})}}{{comment.user}}</div>
      <div class="comments__text" v-html="htmlText"></div>
    </li>
  `,
};
