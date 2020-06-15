from telegram import ParseMode
from telegram.ext import ConversationHandler

from core.services import users, vacations
from core.resources import strings, keyboards
from core.bot.utils import Navigation


LIST, RESUMES = range(2)


def vacations_list(update, context):
    query = update.callback_query
    language = context.user_data['user'].get('language')
    user_id = context.user_data['user'].get('id')
    user_vacations = users.get_user_vacations(user_id)
    if len(user_vacations) == 0:
        empty_message = strings.get_string('vacations.empty_list', language)
        query.answer(text=empty_message, show_alert=True)
        return ConversationHandler.END
    list_message = strings.get_string('vacations.resumes.select', language)
    list_keyboard = keyboards.get_vacations_keyboard(user_vacations, language, include_create_button=False)
    query.edit_message_text(text=list_message, reply_markup=list_keyboard)
    return LIST


def vacations_for_resume(update, context):
    query = update.callback_query
    language = context.user_data['user'].get('language')
    vacation_id = query.data.split(':')[1]
    if vacation_id == 'back':
        Navigation.to_account(update, context)
        return ConversationHandler.END
    resumes = vacations.get_resumes_for_vacation(vacation_id)
    if len(resumes) == 0:
        empty_message = strings.get_string('vacations.resumes.empty', language)
        query.answer(text=empty_message, show_alert=True)
        return LIST
    context.user_data['found_resumes'] = resumes
    context.user_data['current_page'] = 1
    first_resume = resumes[0]
    user = users.user_exists(first_resume.get('user_id'))
    vacation_message = strings.from_resume(first_resume, language, for_vacation=True)
    vacations_keyboard = keyboards.get_list_paginated_keyboard(resumes, language, user)
    query.edit_message_text(text=vacation_message, reply_markup=vacations_keyboard)
    return RESUMES


def paginated_vacations(update, context):
    query = update.callback_query
    language = context.user_data['user'].get('language')
    page = query.data.split(':')[1]
    if page == 'back':
        user_id = context.user_data['user'].get('id')
        user_vacations = users.get_user_vacations(user_id)
        list_message = strings.get_string('vacations.resumes.select', language)
        list_keyboard = keyboards.get_vacations_keyboard(user_vacations, language, include_create_button=False)
        query.edit_message_text(text=list_message, reply_markup=list_keyboard)
        return LIST
    if int(page) == context.user_data['current_page']:
        return RESUMES
    resume = context.user_data['found_resumes'][int(page) - 1]
    user = users.user_exists(resume.get('user_id'))
    resume_message = strings.from_resume(resume, language, for_vacation=True)
    resume_keyboard = keyboards.get_list_paginated_keyboard(context.user_data['found_resumes'],
                                                            language, user, current_page=int(page))
    query.edit_message_text(text=resume_message, reply_markup=resume_keyboard)
    context.user_data['current_page'] = int(page)
    return RESUMES
