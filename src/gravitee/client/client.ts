import { HttpService, Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Token } from 'src/authentication/token.type';
import { Subscription } from 'src/gravitee/types/subscription.type';
import * as debugFactory from 'debug';
import {} from 'src/gravitee/types/application.type';
import {
  SubscriptionListDTO,
  SubscriptionListUnitDTO,
} from 'src/gravitee/client/dtos/subscription-list.dto';
import { SubscriptionMapper } from 'src/gravitee/client/dtos/mapper';
import { KeyDTO } from 'src/gravitee/client/dtos/key.dto';

const debug = debugFactory('application:gravitee-client');

@Injectable()
export class GraviteeClient {
  constructor(
    private readonly httpService: HttpService,
    private readonly configService: ConfigService,
  ) {}

  async listApiParticulierActiveSubscriptions(
    token: Token,
  ): Promise<Subscription[]> {
    const graviteeUrl = this.configService.get<string>('GRAVITEE_URL');

    const { data: subscriptions } = await this.httpService
      .get<SubscriptionListDTO>(`${graviteeUrl}/subscriptions`, {
        headers: { Authorization: `Bearer ${token}` },
        params: {
          statuses: 'ACCEPTED',
        },
      })
      .toPromise();

    debug('list user subscriptions', subscriptions);

    return Promise.all(
      subscriptions.map(async (subscription) => {
        const keys = await this.getSubscriptionKeys(token, subscription);

        return SubscriptionMapper.dtoToDomain(keys, subscription);
      }),
    );
  }

  private async getSubscriptionKeys(
    token: Token,
    subscription: SubscriptionListUnitDTO,
  ): Promise<[KeyDTO]> {
    const graviteeUrl = this.configService.get<string>('GRAVITEE_URL');

    const { data: keys } = await this.httpService
      .get<[KeyDTO]>(
        `${graviteeUrl}/applications/${subscription.application.id}/subscriptions/${subscription.id}/keys`,
        {
          headers: { Authorization: `Bearer ${token}` },
          params: {
            include: 'keys',
          },
        },
      )
      .toPromise();

    debug('subscription keys', keys);

    return keys;
  }
}
