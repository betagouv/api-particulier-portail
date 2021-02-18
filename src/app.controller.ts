import { Controller, Get, Render, UseFilters, UseGuards } from '@nestjs/common';
import { ForbiddenExceptionFilter } from 'src/authentication/forbidden-exception.filter';
import { IsAuthenticatedGuard } from 'src/authentication/is-authenticated.guard';

@Controller()
@UseGuards(IsAuthenticatedGuard)
@UseFilters(ForbiddenExceptionFilter)
export class AppController {
  @Get()
  @Render('index')
  getHello() {
    return { message: 'Hello' };
  }
}
