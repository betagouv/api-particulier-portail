import { HttpService, Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Token } from 'src/authentication/token.type';
import { Subscription } from 'src/gravitee/types/subscription.type';
import * as debugFactory from 'debug';
import {} from 'src/gravitee/types/application.type';
import {
  SubscriptionDTOId,
  SubscriptionUnitDTO,
} from 'src/gravitee/client/dtos/subscription-unit.dto';
import {
  ApplicationDTO,
  ApplicationDTOId,
} from 'src/gravitee/client/dtos/application-unit.dto';
import { SubscriptionListDTO } from 'src/gravitee/client/dtos/subscription-list.dto';
import { SubscriptionMapper } from 'src/gravitee/client/dtos/mapper';

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

    const {
      data: { data: subscriptions },
    } = await this.httpService
      .get<{ data: SubscriptionListDTO }>(`${graviteeUrl}/subscriptions`, {
        headers: { Authorization: `Bearer ${token}` },
        params: {
          statuses: 'ACCEPTED',
        },
      })
      .toPromise();

    debug('list user subscriptions', subscriptions);

    return Promise.all(
      subscriptions.map(async (subscription) => {
        const application = await this.getApplicationDetails(
          token,
          subscription.application,
        );
        const subsciptionDetails = await this.getSubscriptionDetails(
          token,
          subscription.id,
        );

        return SubscriptionMapper.dtoToDomain(subsciptionDetails, application);
      }),
    );
  }

  private async getSubscriptionDetails(
    token: Token,
    subscriptionId: SubscriptionDTOId,
  ): Promise<SubscriptionUnitDTO> {
    const graviteeUrl = this.configService.get<string>('GRAVITEE_URL');

    const { data: subscription } = await this.httpService
      .get<SubscriptionUnitDTO>(
        `${graviteeUrl}/subscriptions/${subscriptionId}`,
        {
          headers: { Authorization: `Bearer ${token}` },
          params: {
            include: 'keys',
          },
        },
      )
      .toPromise();

    debug('subscription details', subscription);

    return subscription;
  }

  private async getApplicationDetails(
    token: Token,
    applicationId: ApplicationDTOId,
  ): Promise<ApplicationDTO> {
    const graviteeUrl = this.configService.get<string>('GRAVITEE_URL');

    const { data: application } = await this.httpService
      .get<ApplicationDTO>(`${graviteeUrl}/applications/${applicationId}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .toPromise();

    debug('application details', application);

    return application;
  }
}
