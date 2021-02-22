import {
  Controller,
  Get,
  Render,
  Session,
  UseFilters,
  UseGuards,
} from '@nestjs/common';
import { ForbiddenExceptionFilter } from 'src/authentication/forbidden-exception.filter';
import { IsAuthenticatedGuard } from 'src/authentication/is-authenticated.guard';
import { SubscriptionService } from 'src/gravitee/subscription.service';

@Controller()
@UseGuards(IsAuthenticatedGuard)
@UseFilters(ForbiddenExceptionFilter)
export class AppController {
  constructor(private readonly subscriptionService: SubscriptionService) {}

  @Get()
  @Render('index')
  async getHello(@Session() session: Record<string, any>) {
    const subscriptions = await this.subscriptionService.listActiveSubscriptions(
      session.token,
    );
    return { subscriptions };
  }
}
