import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Token } from 'src/authentication/token.type';
import { GraviteeClient } from 'src/gravitee/client/client';
import { Subscription } from 'src/gravitee/types/subscription.type';

@Injectable()
export class SubscriptionService {
  constructor(
    private readonly configService: ConfigService,
    private readonly graviteeClient: GraviteeClient,
  ) {}

  async listActiveSubscriptions(token: Token) {
    const subscriptions = await this.graviteeClient.listApiParticulierActiveSubscriptions(
      token,
    );
    return this.filterApiParticulierSubscriptions(subscriptions);
  }

  private filterApiParticulierSubscriptions(
    rawSubscriptions: Subscription[],
  ): Subscription[] {
    return rawSubscriptions.filter(
      (subscription) =>
        subscription.plan ===
        this.configService.get<string>('API_PARTICULIER_PLAN_ID'),
    );
  }
}
