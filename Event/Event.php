<?php
namespace Wizin\Bundle\SimpleCmsBundle\Event;

class Event
{
    const ON_FRONT_CONTROLLER = 'wizin_simple_cms.event.front_controller';
    const ON_ADMIN_CONTROLLER = 'wizin_simple_cms.event.admin_controller';
    const ON_INJECT_VARIABLES = 'wizin_simple_cms.event.inject_variables';
}