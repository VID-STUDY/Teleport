from telegram import ParseMode
from telegram.ext import MessageHandler
from telegram.error import BadRequest

from core.resources import utils, images
from core.services import settings
from .utils import Filters


def faq(update, context):
    faq_message = settings.get_settings().get('faq')
    faq_message = utils.replace_new_line(faq_message)
    image = images.get_faq_image()
    if image:
        chat_id = update.message.chat_id
        message = context.bot.send_photo(chat_id=chat_id, photo=image, caption=faq_message, parse_mode=ParseMode.HTML)
    else:
        message = update.message.reply_text(text=faq_message, parse_mode=ParseMode.HTML)
    if 'faq_message_id' in context.user_data:
        try:
            context.bot.delete_message(chat_id=update.message.chat_id, message_id=context.user_data['faq_message_id'])
        except BadRequest:
            pass
    context.user_data['faq_message_id'] = message.message_id


faq_handler = MessageHandler(Filters.FaqFilter(), faq)
